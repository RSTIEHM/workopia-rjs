<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;

class ListingController
{
    protected $db;

    // CONSTRUCT =====================
    public function __construct()
    {
        $config = require basePath("config/db.php");
        $this->db = new Database($config);
    }
    // INDEX =====================
    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();
        loadView('/listings/index', ['listings' => $listings]);
    }
    // CREATE =====================
    public function create()
    {
        loadView('/listings/create');
    }
    // SHOW =====================
    public function show($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound('Listing Not Found');
            return;
        }

        loadView('listings/show', ['listing' => $listing]);
    }
    // STORE =====================
    public function store()
    {
        $allowedFields = [
            "title", "description", "salary", "tags",
            "requirements", "benefits", "company",
            "address", "city", "state", "phone", "email"
        ];
        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));
        $newListingData['user_id'] = Session::get('user')['id'];
        // HELPERS SANITIZE FUNCTION
        $newListingData = array_map('sanitize', $newListingData);
        $requiredFields = ['title', 'description', 'email', 'city', 'state'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . " is required";
            }
        }
        if (!empty($errors)) {
            // RELOAD VIEW WITH ERRROS
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            // SUMBIT DATA
            $fields = [];
            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }
            $fields = implode(", ", $fields);
            $value = [];
            foreach ($newListingData as $field => $value) {
                if ($value === "") {
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;
            }
            $values = implode(", ", $values);
            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
            $this->db->query($query, $newListingData);
            redirect("/listings");
            // inspect($fields);
            // inspect($values);
        }
    }
    // DESTROY ======================
    public function destroy($params)
    {
        $id = $params['id'];
        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }
        // AUTHORIZE DELETE TO USER 
        // if (Session::get('user')['id'] !== $listing->user_id) {
        //     $_SESSION['error_message'] = "You are not authorized to delete this listing";
        //     return redirect("/listings/{$listing->id}");
        // }

        if (!Authorization::isOwner($listing->user_id)) {
            $_SESSION['error_message'] = "You are not authorized to delete this listing!!!";
            return redirect("/listings/{$listing->id}");
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $params);
        $_SESSION['success_message'] = 'Listing Deleted Succesfully';
        redirect('/listings');
    }

    // DESTROY ======================
    public function edit($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound('Listing Not Found');
            return;
        }

        loadView('listings/edit', ['listing' => $listing]);
    }

    public function update($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound('Listing Not Found');
            return;
        }
        $allowedFields = [
            "title", "description", "salary", "tags",
            "requirements", "benefits", "company",
            "address", "city", "state", "phone", "email"
        ];
        $updateValues = [];
        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updateValues = array_map('sanitize', $updateValues);

        $requiredFields = ['title', 'description', 'email', 'city', 'state'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . " is required";
            }
        }
        if (!empty($errors)) {
            loadView('listings/edit', ['listing' => $listing, 'errors' => $errors]);
            exit;
        } else {
            $updateFields = [];
            foreach (array_keys($updateValues) as $field) {

                $updateFields[] = "{$field} = :{$field}";
            }
            $updateFields = implode(", ", $updateFields);
            $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";
            $updateValues['id'] = $id;
            $this->db->query($updateQuery, $updateValues);
            $_SESSION['success_message'] = "Listing Updated";
            redirect("/listings/" . $id);
        }
    }
}
