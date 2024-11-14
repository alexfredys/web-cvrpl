<?php
include '../../koneksi/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_dash = $_POST['id_dash'];
    $deskripsi_dash_new = $_POST['deskripsi_dash'];
    $matkul_dash = $_POST['matkul_dash'];
    $dosen_dash = $_POST['dosen_dash'];

    $file_name = $_FILES['file_dash']['name']; // Uploaded file name
    $file_temp = $_FILES['file_dash']['tmp_name']; // Temporary file path on the server
    $file_destination = "foto/" . $file_name; // Destination path to store the file

    if (move_uploaded_file($file_temp, $file_destination)) {
        // File uploaded successfully, get file data
        $file_data = file_get_contents($file_destination);

        // Set $nip based on $dosen_dash value
        if ($dosen_dash == 'fahri') {
            $nip = '234';
        } else {
            $nip = '567';
        }

        // Prepare SQL query to prevent SQL injection
        $sql = "UPDATE dashboard SET deskripsi_dash=?, matkul_dash=?, dosen_dash=?, file_dash=? WHERE id_dash=?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssssi", $deskripsi_dash_new, $matkul_dash, $dosen_dash, $file_data, $id_dash);

        if ($stmt->execute()) {
            // Update dashboard successful, update dashboard_dosen
            $sql2 = "UPDATE dashboard_dosen SET status_dash='menunggu' WHERE id_dash=?";
            $stmt2 = $koneksi->prepare($sql2);
            $stmt2->bind_param("i", $id_dash);

            if ($stmt2->execute()) {
                // Redirect to the page after successful edit
                header("Location: ../dashboard1.php");
                exit();
            } else {
                echo "Error updating dashboard_dosen: " . $koneksi->error;
            }
        } else {
            echo "Error updating dashboard: " . $koneksi->error;
        }
    } else {
        echo "Gagal mengunggah file.";
    }
}
?>
