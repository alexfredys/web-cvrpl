<?php
session_start();
include '../../koneksi/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim_mhs = $_SESSION['nim'];
    $judul_dash = $_POST['judul_dash'];
    $deskripsi_dash = $_POST['deskripsi_dash'];
    $matkul_dash = $_POST['matkul_dash'];
    $dosen_dash = $_POST['dosen_dash'];

    // Set $nip based on $dosen_dash value
    if ($dosen_dash == 'fahri') {
        $nip = '234';
    } else if ($dosen_dash == 'andre') {
        $nip = '567';
    } else {
        $nip = '789';
    }

    $file_name = $_FILES['file_dash']['name']; // Uploaded file name
    $file_temp = $_FILES['file_dash']['tmp_name']; // Temporary file path on the server
    $file_type = $_FILES['file_dash']['type']; // Uploaded file type

    $file_destination = "foto/" . $file_name; // Destination path to store the file

    if (move_uploaded_file($file_temp, $file_destination)) {
        // File uploaded successfully, store information in the database
        $file_data = file_get_contents($file_destination); // Get file data

        // Prepare SQL query to prevent SQL injection
        $sql = "INSERT INTO dashboard (nim_mhs, nip, judul_dash, deskripsi_dash, matkul_dash, dosen_dash, file_dash)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sssssss", $nim_mhs, $nip, $judul_dash, $deskripsi_dash, $matkul_dash, $dosen_dash, $file_data);

        if ($stmt->execute()) {
            // Data successfully saved, redirect to the dashboard
            $id_dashboard_terakhir = $koneksi->insert_id;

            $sql2 = "INSERT INTO dashboard_dosen (id_dash, nama_dosen, status_dash) VALUES (?, ?, 'menunggu')";
            $stmt2 = $koneksi->prepare($sql2);
            $stmt2->bind_param("is", $id_dashboard_terakhir, $dosen_dash);

            if ($stmt2->execute()) {
                $stmt->close();
                $stmt2->close();
                header("Location: ../dashboard1.php");
                exit();
            } else {
                echo "Error: " . $stmt2->error;
            }
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // If file upload failed
        echo "Gagal mengunggah file.";
    }
}

$koneksi->close();
?>
