<?php
require_once __DIR__ ."/../models/session.php";

class SessionController {
    private $sessionModel;

    public function __construct($sessionModel) {
        $this->sessionModel = $sessionModel;
    }

    public function index() {
        $sessions = $this->sessionModel->getAll();
        require __DIR__ . "/../views/sessions/index.php";
    }

    public function show($id) {
        $session = $this->sessionModel->getById($id);
        require __DIR__ . "/../views/sessions/show.php";
    }

    public function create() {
        $session = $this->sessionModel->create();
        
        require __DIR__ . "/../views/sessions/create.php";
    }

    public function store($data) {
        $this->sessionModel->create($data['reservation_id'], $data['game_id'], $data['table_id'], $data['start_time'], $data['end_time'], $data['status']);
        header("Location: /sessions");
    }

    public function edit($id) {
        $session = $this->sessionModel->getById($id);
        require __DIR__ . "/../views/sessions/edit.php";
    }

    public function update($id, $data) {
        $this->sessionModel->update($id, $data['reservation_id'], $data['game_id'], $data['table_id'], $data['start_time'], $data['end_time'], $data['status']);
        header("Location: /sessions");
    }

    public function destroy($id) {
        $this->sessionModel->delete($id);
        header("Location: /sessions");
    }
}
?>
