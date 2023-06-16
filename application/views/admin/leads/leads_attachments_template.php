<?php defined('BASEPATH') or exit('No direct script access allowed');
$data = '<div class="row">';
$i = 0;
foreach($attachments as $attachment) {
    $attachment_url = site_url('download/file/lead_attachment/'.$attachment['id']);
    if(!empty($attachment['external'])){
        $attachment_url = $attachment['external_link'];
    }
    $data .= '<div class="display-block lead-attachment-wrapper">';
    $data .= '<div class="col-md-10">';
    $data .= '<div class="pull-left"><img id="myImg'.$i.'" height="150px" width="150px" src="'.$attachment_url.'"></div>';
    $data .= '<a href="'.$attachment_url.'" target="_blank">'.$attachment['file_name'].'</a>';
    $data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
    $data .= '</div>';
    $data .= '<div class="col-md-2 text-right">';
    if($attachment['staffid'] == get_staff_user_id() || is_admin()){
    $data .= '<a href="#" class="text-danger" onclick="delete_lead_attachment(this,'.$attachment['id'].', '.$attachment['rel_id'].'); return false;"><i class="fa fa fa-times"></i></a>';
    }
    $data .= '</div>';
    $data .= '<div class="clearfix"></div><hr/>';
    $data .= '</div>';
    $data .= '<div id="myModal'.$i.'" class="modal_new">
        <span class="close close'.$i.'">&times;</span>
        <img class="modal-content" id="img01'.$i.'">
        <div id="caption"></div>
    </div>';
    $data .= '<script>
    // Get the modal
    var modal = document.getElementById("myModal'.$i.'");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img = document.getElementById("myImg'.$i.'");
    var modalImg = document.getElementById("img01'.$i.'");
    var captionText = document.getElementById("caption");
    img.onclick = function(){
    modal.style.display = "block";
    modalImg.src = this.src;
    captionText.innerHTML = this.alt;
    }

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close'.$i.'")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() { 
    modal.style.display = "none";
    }
    </script>';
    $i++;
}
$data .= '</div>';
echo $data;
