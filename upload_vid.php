<?php
    session_start();
    include 'connection.php';
    include 'function.php';
    
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    date_default_timezone_set('Asia/Jakarta');
    $timestamp = date('d/m/Y h:i A');

    if (isset($_POST['btnSubmit'])&&isset($_FILES['my_video'])) {

        
        $videoname = $_FILES['my_video']['name'];
        $tmp_name = $_FILES['my_video']['tmp_name'];
        $error = $_FILES['my_video']['error'];
        
        $fn = (($_POST['nama'])!="") ? $_POST['nama'] : $rmbr;
        $judul = (($_POST['judul'])!="") ? $_POST['judul'] : $videoname;
        $kategori=$_POST['kategori'];


        $hour=$_POST['hour'];
        $miniute=$_POST['miniute'];
        $second=$_POST['second'];

        $hours = ($hour=='0'||$hour=="0"||$hour==0) ? null:$hour." Jam ";
        $miniutes = ($miniute=='0'||$miniute=="0"||$miniute==0) ? null:$miniute." Menit ";
        $seconds = ($second=='0'||$second=="0"||$second==0) ? null:$second." Detik ";

        $duration=$hours.$miniutes.$seconds;

        if($error===0){
            $videoex=pathinfo($videoname,PATHINFO_EXTENSION);

            $video_ex_lc=strtolower($videoex);

            $allowed_exs=array("mp4",'webm','avi','flv','mov');

            if(in_array($video_ex_lc, $allowed_exs)){
                $newvideoname = uniqid("video-",true).".".$video_ex_lc;
                $video_upload_path='uploads/'.$newvideoname;
                $move=move_uploaded_file($tmp_name,$video_upload_path);

                $sql="INSERT INTO `files`(
                    `author`,
                    `judul`,
                    `file_type_id`,
                    `kategori`,
                    `isi`
                )
                VALUES(
                    '$fn',
                    '$judul',
                    'video',
                    '$kategori',
                    '$newvideoname'
                );";
                $run=mysqli_query($conn,$sql);
                
                $ssql="SELECT * FROM `files` WHERE `isi`='$newvideoname'";
                $rrun=mysqli_query($conn,$ssql);
                $row=mysqli_fetch_assoc($rrun);
                $iid=$row['id'];
                $jud=$row['judul'];
                $typ=$row['file_type_id'];
                $ca=$row['created_at'];
                $ua=$row['update_at'];

                $size=filesize($video_upload_path);

                $sssql="INSERT INTO `assignment`(
                    `file_id`,
                    `version`,
                    `production_year`,
                    `created_by_user_id`,
                    `file_size`,
                    `duration`,
                    `length`,
                    `url`,
                    `created_at`
                )
                VALUES(
                    '$iid',
                    '0',
                    '2022',
                    '$fn',
                    '$size',
                    '$duration',
                    '8',
                    '$video_upload_path',
                    '$ca'
                )";

                $rrrun=mysqli_query($conn,$sssql);
                
                if($run&&$move&&$rrrun){
                    header('Location:h1.php?pilihan=Video&sm=Video Berhasil ditambahkan');
                }else{
                    header("Location:h1.php?pilihan=Video&em=There was an error occured");
                }
            }else{
                header("Location:h1.php?pilihan=Video&em=Tipe file tidak didukung");
            }
        }
}else{
    header("Location:h1.php?pilihan=Video&em=There was an error occured");
}

?>
