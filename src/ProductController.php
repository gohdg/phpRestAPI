<?php

class ProductController
{
    public function __construct(private ProductGateway $gateway)
    {

    }
    public function processRequest(string $method, ?string $id): void
    {
        // var_dump($method, $id);
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $product = $this->gateway->get($id);

        echo json_encode($product);
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case 'GET':
                echo json_encode($this->gateway->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {
                    http_response_code(422); // 입력값이 유효하지 않거나, 필수필드 누락시
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $id = $this->gateway->create($data);

                http_response_code(201);
                echo json_encode([
                    "message" => "Product created",
                    "id" => $id,
                ]);
                break;

            default:
                http_response_code(405); // Method Not Allowed
                header("Allow: GET, POST");
                break;
        }
    }

    private function getValidationErrors(array $data): array
    {
        $errors = [];

        if (empty($data["name"])) {
            $errors[] = "name is required";
        }

        if (array_key_exists("size", $data)) {

            if (filter_var($data["size"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "size must be an integer";
            }

        }

        return $errors;

    }
}
