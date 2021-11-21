<?php

require_once "../defines.php";
require_once ROOT."/classes/question.class.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/conferences.class.php";
require_once ROOT."/classes/lecture.class.php";

start_session_if_none();

if(isset($_POST['lecture_id'])){
	if(isset($_POST['html'])){
		$ret = Question::get_questions_by_lecture_id($_POST['lecture_id']);

		if($ret === false){
			echo_json_response($ret, Question::$error_message);
			return;
		}

		$html = '<div class="card">
                <div class="card-header">Otázky</div>
                <div class="card-body height3">
                    <ul class="chat-list">';

        $delete = "";

        if(isset($_SESSION['user'])){
            $user_id = $_SESSION['user']->get_user_data()['id'];
            $role = $_SESSION['user']->get_user_data()['role'];

            $is_owner_or_admin = $user_id == Lecture::get_conference_owner($_POST['lecture_id']);
            $is_owner_or_admin = $is_owner_or_admin || $user_id == Lecture::get_lecture_by_id($_POST['lecture_id'])['id_user'];
            $is_owner_or_admin = $is_owner_or_admin || ($role == USER_ADMIN); 

            if($is_owner_or_admin){
                $delete = '<span style="color: #343a40" class="delete-span" onclick="delete_question(%d)"><u>Odstrániť</u></span>';
            }
            
        }
        
        foreach($ret as $key => $question){
            $user_data = User::get_user_data_by_id($question['user_id']);
            if($user_data === false){
                $name = "Unknown";
            }else{
                $name = $user_data['name'];
            }


        	$html.= '<li class="in" id="question-id'.$question['id'].'">
                                <div class="chat-message">
                                    <h5>'.$name.'</h5>
                                    <p>'.htmlspecialchars($question['question']).'</p>'
                                    .sprintf($delete,$question['id']).
                                '</div>
                        </li>';
        }
        
        $html .='</ul></div>';
         if(isset($_SESSION['user'])){
                        $html.= '<div class="input-group">
                        <input class="form-control" type="text" id="txt-send"/>
                        <div class="input-group-append">
                            <span style="padding:0;" class="input-group-text" id="btn-send-span"><button class="btn btn-primary" id="btn-send-question">Odoslať</button>
                            </span>
                        </div>
                        </div>
                    </div>'
                    ;
                    }         

		echo_json_response(true, $html);
		return;
	}
    else if(isset($_POST['msg'])){
        if(!isset($_SESSION['user'])){
             echo_json_response(false, "Musíš byť prihlásený.");
            return;
        }
        if(strlen($_POST['msg']) > 249){
            echo_json_response(false, "Otázka musí byť kratšia ako 250 znakov.");
            return;
        }
        if(strlen($_POST['msg']) < 5){
            echo_json_response(false, "Otázka musí byť dlhšia ako 5 znakov.");
            return;
        }

        $res = Question::add_question($_POST['lecture_id'],$_POST['msg'],$_SESSION['user']->get_user_data()['id']);

        echo_json_response($res, Question::$error_message);
        return;

    }
	else if(isset($_POST['delete'])){
        if(!isset($_SESSION['user'])){
            echo_json_response(false, "Neprihlásený užívateľ.");
            return;
        }
        
        $user_id = $_SESSION['user']->get_user_data()['id'];
        $role = $_SESSION['user']->get_user_data()['role'];

        $is_owner_or_admin = $user_id == Lecture::get_conference_owner($_POST['lecture_id']);
        $is_owner_or_admin = $is_owner_or_admin || $user_id == Lecture::get_lecture_by_id($_POST['lecture_id'])['id_user'];
        $is_owner_or_admin = $is_owner_or_admin || ($role == USER_ADMIN); 

        if(!$is_owner_or_admin){
            echo_json_response(false, "Nedostatočné oprávnenie.");
            return;
        }
        
        $res = Question::delete_question_by_id_and_conf($_POST['delete'],$_POST['lecture_id']);
        echo_json_response($res, Question::$error_message);
        return;
    }
}