<?php 
$this->load->model('project_roadmap/project_roadmap_model');
$project_roadmap_list = $this->project_roadmap_model->get_filter_widget(get_staff_user_id(),'project_roadmap');
?>
<?php foreach ($project_roadmap_list as $project_roadmap) { ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('project_roadmap'); ?>">
<div class="panel_s user-data">
  <div class="panel-body">
    <div class="widget-dragger"></div>
     <?php $data = $this->project_roadmap_model->view_project_roadmap_helper($project_roadmap['rel_id']); 
     ?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6">
        <?php if(isset($data['project']->charge_code)){
          $name = $data['project']->charge_code.' - '. $data['project']->name;
        }else{
          $name = $data['project']->name;
        } ?>
         <p class="text-dark text-uppercase bold"><?php echo _l('project_roadmap').': '.$name ?></p>
      </div>
         <div class="col-md-3 pull-right">
          <a href="Javascript:void(0);" class="pull-right btn btn-danger btn-icon" data-toggle="tooltip" title="" onclick="remove_project_roadmap_dashboard(<?php echo '' . $project_roadmap['id']; ?>)" data-original-title="<?php echo _l('remove_dashboard'); ?>"><i class="fa fa-compress"></i></a> 
         </div>
         <br>
         <hr class="mtop15" />
      </div>
     <?php $this->load->view('project_roadmap/view_project_roadmap_dashboard', $data); ?>
     </div>
    </div>
  </div>
</div>
<?php }
?>

 

