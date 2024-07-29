<?php

require '../include/dbconfig.php';

function getToDoById($toDoParams) {
    global $conn;

    if ($toDoParams['id'] == null) {
        return handleError("Id Is Not Provided! Please try again later.");
    }

    $id = trim($toDoParams['id']);

    $stmt = $conn->prepare("SELECT id, title, createdAt AS created_at FROM todolist WHERE id = ?");

    if ($stmt === false) {
        $data = [
            'Status' => 500,
            'Message' => 'Internal Server Error: Failed to prepare the SQL statement',
        ];

        header('HTTP/1.0 500 Internal Server Error');
        return json_encode($data);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $toDo = $result->fetch_assoc();

        // format the time according to the requirements (GMT format)
        $dateTime = new DateTime($toDo['created_at']);
        $toDo['created_at'] = $dateTime->format('Y-m-d\TH:i:s.u\Z');

        $data = [
            'Status' => 200,
            'Message' => 'The selected to do list has been fetched successfully',
            'Data' => $toDo
        ];

        header('HTTP/1.0 200 SUCCESS');
    } else {
        $data = [
            'Status' => 404,
            'Message' => 'The provided to do list cannot be found in the system',
        ];

        header('HTTP/1.0 404 The requested data was not found');
    }

    $stmt->close();
    $conn->close();

    return json_encode($data);
}

function getToDoList() {
    global $conn;

    $stmt = $conn->prepare("SELECT id, title, createdAt AS created_at FROM todolist");

    if ($stmt === false) {
        $data = [
            'Status' => 500,
            'Message' => 'Internal Server Error: Failed to prepare the SQL statement',
        ];

        header('HTTP/1.0 500 Internal Server Error');
        return json_encode($data);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $toDoList = $result->fetch_all(MYSQLI_ASSOC);

            // format the time according to the requirements (GMT format)
            foreach ($toDoList as &$item) {
                $dateTime = new DateTime($item['created_at']);
                $item['created_at'] = $dateTime->format('Y-m-d\TH:i:s.u\Z');
            }

            $data = [
                'Status' => 200,
                'Message' => 'All to do lists have been fetched successfully',
                'Data' => $toDoList
            ];

            header('HTTP/1.0 200 SUCCESS');
            return json_encode($data);
        } else {
            $data = [
                'Status' => 404,
                'Message' => 'The requested to do list cannot be found in the system',
            ];

            header('HTTP/1.0 404 The requested data was not found');
            return json_encode($data);
        }
    } else {
        $data = [
            'Status' => 500,
            'Message' => 'Internal Server Error',
        ];

        header('HTTP/1.0 500 Internal Server Error');
        return json_encode($data);
    }

    $stmt->close();
}

function addToDo($toDoInput) {
    global $conn;

    $title = trim($toDoInput['title']);

    if (empty($title)) {
        return handleError("Please enter the title in order to add a new to do");
    }

    $stmt = $conn->prepare("INSERT INTO todolist (title) VALUES (?)");

    if ($stmt === false) {
        $data = [
            'Status' => 500,
            'Message' => 'Internal Server Error: Failed to prepare the SQL statement',
        ];

        header('HTTP/1.0 500 Internal Server Error');
        return json_encode($data);
    }

    $stmt->bind_param("s", $title);
    $executed = $stmt->execute();

    if ($executed) {
        // get the last created id
        $id = $stmt->insert_id;
        $stmt->close();

        // after created the new to do, return the newly created to do to the user
        $stmt = $conn->prepare("SELECT id, title, createdAt AS created_at FROM todolist WHERE id = ?");
        if ($stmt === false) {
            $data = [
                'Status' => 500,
                'Message' => 'Internal Server Error',
            ];
            header('HTTP/1.0 500 Internal Server Error');
            return json_encode($data);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $toDo = $result->fetch_assoc();

            // format the time according to the requirements (GMT format)
            $dateTime = new DateTime($toDo['created_at']);
            $toDo['created_at'] = $dateTime->format('Y-m-d\TH:i:s.u\Z');

            $data = [
                'Status' => 200,
                'Message' => 'The new to do has been added successfully',
                'Data' => $toDo
            ];
            header('HTTP/1.0 200 SUCCESS');
        } else {
            $data = [
                'Status' => 404,
                'Message' => 'The requested data was not found',
            ];
            header('HTTP/1.0 404 Not Found');
        }

        $stmt->close();
    } else {
        $data = [
            'Status' => 500,
            'Message' => 'Internal Server Error',
        ];
        header('HTTP/1.0 500 Internal Server Error');
    }

    $conn->close();
    return json_encode($data);
}

function updateToDo($toDoInput, $toDoParams) {
    global $conn;

    if (!isset($toDoParams['id'])) {
        return handleError('The id is not found in the URL');
    } else if ($toDoParams['id'] == null) {
        return handleError('Please provide the to do Id');
    }

    $id = trim($toDoParams['id']);
    $title = mysqli_real_escape_string($conn, $toDoInput['title']);

    if (empty($title)) {
        return handleError("Please enter the title in order to add a new to do");
    }

    $stmt = $conn->prepare("UPDATE todolist SET title = ? WHERE id = ?");
    if ($stmt === false) {
        $data = [
            'Status' => 500,
            'Message' => 'Internal Server Error',
        ];
        header('HTTP/1.0 500 Internal Server Error');
        return json_encode($data);
    }

    $stmt->bind_param("si", $title, $id);
    $executed = $stmt->execute();

    if ($executed) {
        $stmt = $conn->prepare("SELECT id, title, createdAt AS created_at FROM todolist WHERE id = ?");
        if ($stmt === false) {
            $data = [
                'Status' => 500,
                'Message' => 'Internal Server Error',
            ];
            header('HTTP/1.0 500 Internal Server Error');
            return json_encode($data);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $toDo = $result->fetch_assoc();

            // format the time according to the requirements (GMT format)
            $dateTime = new DateTime($toDo['created_at']);
            $toDo['created_at'] = $dateTime->format('Y-m-d\TH:i:s.u\Z');

            $data = [
                'Status' => 200,
                'Message' => 'The selected to do has been updated successfully',
                'Data' => $toDo
            ];
            header('HTTP/1.0 200 SUCCESS');
        } else {
            $data = [
                'Status' => 404,
                'Message' => 'The requested data was not found',
            ];
            header('HTTP/1.0 404 Not Found');
        }

        $stmt->close();
    } else {
        $data = [
            'Status' => 500,
            'Message' => 'Internal Server Error',
        ];
        header('HTTP/1.0 500 Internal Server Error');
    }

    $conn->close();
    return json_encode($data);
}

function deleteToDo($toDoParams) {
    global $conn;

    if (!isset($toDoParams['id'])) {
        return handleError('Id is not found in the URL');
    } else if ($toDoParams['id'] == null) {
        return handleError('Please provide the to do Id');
    }

    $id = trim($toDoParams['id']);

    $stmt = $conn->prepare("DELETE FROM todolist WHERE id = ?");

    if ($stmt === false) {
        $data = [
            'Status' => 500,
            'Message' => 'Internal Server Error',
        ];
        header('HTTP/1.0 500 Internal Server Error');
        return json_encode($data);
    }

    $stmt->bind_param("i", $id);
    $executed = $stmt->execute();

    if ($executed) {
        if ($stmt->affected_rows > 0) {
            $data = [
                'Status' => 200,
                'Message' => 'Todo item deleted successfully',
            ];
            header("HTTP/1.0 200 SUCCESS");
        } else {
            $data = [
                'Status' => 404,
                'Message' => 'The requested data was not found',
            ];
            header("HTTP/1.0 404 Not Found");
        }
    } else {
        $data = [
            'Status' => 500,
            'Message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
    }

    $stmt->close();
    $conn->close();

    return json_encode($data);
}

function handleError($message) {
    $data = [
        "Status" => 422,
        "Message" => $message
    ];

    header('HTTP/1.0 422 Unprocessable Content Client Error');
    return json_encode($data);
}

?>
