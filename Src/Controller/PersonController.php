<?php


namespace Src\Controller;
use Src\TableGateways\PersonGateway;

class PersonController
{
    private $db;
    private $requestMethod;
    private $userId;

    private $personGateway;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;

        $this->personGateway = new PersonGateway($db);
    }


    private function setupSuccessResponse($data){
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($data);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function validatePerson($input)
    {
        if (! isset($input['firstname'])) {
            return false;
        }
        if (! isset($input['lastname'])) {
            return false;
        }
        return true;
    }



    private function getALlUsers(){
        $result = $this->personGateway->findAll();
        return $this->setupSuccessResponse($result);

    }

    private function getUserById($id){
        $result = $this->personGateway->find($id);
        if(!$result){
            return $this->notFoundResponse();
        }
        return $this->setupSuccessResponse($result);
    }

    private function createUserFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->personGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $result = $this->personGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        //php://input is a read-only stream that allows you to read raw data from the request body.
        // php://input is not available with enctype="multipart/form-data".
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->personGateway->update($id, $input);
//        $response['status_code_header'] = 'HTTP/1.1 200 OK';
//        $response['body'] = null;
//        return $response;
        return $this->setupSuccessResponse(null);
    }

    private function deleteUser($id)
    {
        $result = $this->personGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->personGateway->delete($id);
//        $response['status_code_header'] = 'HTTP/1.1 200 OK';
//        $response['body'] = null;
        return $this->setupSuccessResponse(null);

    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->userId) {
                    $response = $this->getUserById($this->userId);
                } else {
                    $response = $this->getAllUsers();
                };
                break;
            case 'POST':
                $response = $this->createUserFromRequest();
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->userId);
                break;
            case 'DELETE':
                $response = $this->deleteUser($this->userId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }





}