<?php

class routepage {

    public static function login() {
        session_start();
        if (count($_POST) != 0) {
            $user = $_POST["user"];
            $password = md5($_POST["password"]);

            $check = new UserManager();
            $check->load_users();
            $result = $check->login_check($user, $password);
            if ($result != null) {
                $_SESSION = array(
                    "LOGIN" => "CORRECT",
                    "USER_ID" => $result["USER_ID"],
                    "FULL_NAME" => $result["FIRST_NAME"] . "  " . $result["LAST_NAME"],
                    "DEPARTMENT" => $result["DEPARTMENT"],
                );
                echo json_encode(array("message" => "correct"));
            } else {
                echo json_encode(array("message" => "incorrect"));
            }
        } else {
            echo "no data to send";
        }
    }

    public static function add_user() {
        $data = json_decode(file_get_contents("php://input"), true);
        $jf = new jsonformat();
        if ($jf->prepare($data, ADDUSER)[0] == "match") {
            $rs = jsonfile::load("./store/users.json");
            $jr = new jsonrule($rs);
            if ($jr->is_exist("username", $data["username"])) {
                $build = new builder();
                $build->create($rs);
                $data["password_hash"] = md5($data["password_hash"]);
                $data["group"] = "0";
                $data["time_created"] = date("Y-m-d h:i:s");
                $data["status"] = "padding";
                $build->add($data);
                jsonfile::save("./store/users.json", $build->read());
                echo json_encode(array(
                    'message' => 'success',
                    'profile' => $build->read()
                ));
            } else {
                echo json_encode(array(
                    'message' => 'exist_user',
                    'profile' => $data["username"] . ' already exist'
                ));
            }
        } else {
            echo json_encode(array(
                'message' => 'error',
                'profile' => $jf->prepare($data, ADDUSER)[0]
            ));
        }
    }

    public function edit_user() {
        $data = json_decode(file_get_contents("php://input"), true);
        $jf = new jsonformat();
        if ($jf->prepare($data, ADDUSER)[0] == "match") {
            $rs = jsonfile::load("./store/users.json");
            $jr = new jsonrule($rs);
            $build = new builder();
            $build->create($rs);
            $jr = new jsonrule($rs);
            $position = $jr->get_position("username", $data["username"]);
            $build->update($position, "first_name", $data["first_name"]);
            $build->update($position, "last_name", $data["last_name"]);
            $build->update($position, "department", $data["department"]);
            $build->update($position, "password_hash", md5($data["password_hash"]));
            $build->update($position, "group", "0");
            $build->update($position, "time_created", date("Y-m-d h:i:s"));
            $build->update($position, "status", "padding");
            jsonfile::save("./store/users.json", $build->read());
            echo json_encode(array(
                'message' => 'update',
                'profile' => $build->read()
            ));
        } else {
            echo json_encode(array(
                'message' => 'error',
                'profile' => $jf->prepare($data, ADDUSER)[0]
            ));
        }
    }

    public static function get_users() {
        //$data = json_decode(file_get_contents("php://input"), true);
        $rs = jsonfile::load("./store/users.json");
        $build = new builder();
        $build->create($rs);
        echo json_encode(array(
            'message' => 'success',
            'profile' => $build->read()
        ));
    }

    public static function remove_user() {
        $data = json_decode(file_get_contents("php://input"), true);
        $rs = jsonfile::load("./store/users.json");
        $build = new builder();
        $build->create($rs);
        $jr = new jsonrule($rs);
        $position = $jr->get_position("username", $data["username"]);
        $drs = $build->delete($position);
        if ($drs) {
            jsonfile::save("./store/users.json", $build->read());
            echo json_encode(array(
                'message' => 'removed',
                'profile' => $data["username"]
            ));
        } else {
            echo json_encode(array(
                'message' => 'remove_fail',
                'profile' => 'can\'t remove ' . $data["username"]
            ));
        }
    }

    public function activate_user() {
        $data = json_decode(file_get_contents("php://input"), true);
        $rs = jsonfile::load("./store/users.json");
        $build = new builder();
        $build->create($rs);
        $jr = new jsonrule($rs);
        $position = $jr->get_position("username", $data["username"]);
        $act = array();
        if ($build->get($position)["status"] == "padding" || $build->get($position)["status"] == "active") {
            $act = "non_active";
        } else {
            $act = "active";
        }
        $drs = $build->update($position, "status", $act);
        if ($drs) {
            jsonfile::save("./store/users.json", $build->read());
            echo json_encode(array(
                'message' => $act,
                'profile' => $data["username"]
            ));
        } else {
            echo json_encode(array(
                'message' => $act . '_fail',
                'profile' => 'can\'t ' . $act . $data["username"]
            ));
        }
    }

    public static function logout() {
        session_start();
        session_destroy();
        header("Location: http://localhost/crud_json");
    }

}
