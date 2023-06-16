warning: LF will be replaced by CRLF in application/helpers/custom_fields_helper.php.
The file will have its original line endings in your working directory
[1mdiff --git a/application/helpers/custom_fields_helper.php b/application/helpers/custom_fields_helper.php[m
[1mindex 525e07f..42f3f40 100644[m
[1m--- a/application/helpers/custom_fields_helper.php[m
[1m+++ b/application/helpers/custom_fields_helper.php[m
[36m@@ -9,7 +9,7 @@[m [mdefined('BASEPATH') or exit('No direct script access allowed');[m
  * @param  array $items_cf_params          used only for custom fields for items operations[m
  * @return mixed[m
  */[m
[31m-function render_custom_fields($belongs_to, $rel_id = false, $where = [], $items_cf_params = [])[m
[32m+[m[32mfunction render_custom_fields($belongs_to, $rel_id = false, $where = [], $items_cf_params = [], $include_ids = [])[m
 {[m
     // Is custom fields for items and in add/edit[m
     $items_add_edit_preview = isset($items_cf_params['add_edit_preview']) && $items_cf_params['add_edit_preview'] ? true : false;[m
[36m@@ -44,6 +44,10 @@[m [mfunction render_custom_fields($belongs_to, $rel_id = false, $where = [], $items_[m
         }[m
 [m
         foreach ($fields as $field) {[m
[32m+[m[32m            if (!in_array($field['id'], $include_ids)) {[m
[32m+[m[32m                continue;[m
[32m+[m[32m            }[m
[32m+[m[41m            [m
             if ($field['only_admin'] == 1 && !$is_admin) {[m
                 continue;[m
             }[m
[36m@@ -135,7 +139,6 @@[m [mfunction render_custom_fields($belongs_to, $rel_id = false, $where = [], $items_[m
             }[m
 [m
             $field_name = $field['name'];[m
[31m-[m
             if ($field['type'] == 'input' || $field['type'] == 'number') {[m
                 $t = $field['type'] == 'input' ? 'text' : 'number';[m
                 $fields_html .= render_input($cf_name, $field_name, $value, $t, $_input_attrs);[m
[1mdiff --git a/application/models/Tasks_model.php b/application/models/Tasks_model.php[m
[1mindex 15397e5..df4f799 100644[m
[1m--- a/application/models/Tasks_model.php[m
[1m+++ b/application/models/Tasks_model.php[m
[36m@@ -433,6 +433,7 @@[m [mclass Tasks_model extends App_Model[m
         $data['dateadded']             = date('Y-m-d H:i:s');[m
         $data['addedfrom']             = $clientRequest == false ? get_staff_user_id() : get_contact_user_id();[m
         $data['is_added_from_contact'] = $clientRequest == false ? 0 : 1;[m
[32m+[m[32m        $data['report_document'] = json_encode($data['report_document']);[m
 [m
         $checklistItems = [];[m
         if (isset($data['checklist_items']) && count($data['checklist_items']) > 0) {[m
[36m@@ -675,6 +676,7 @@[m [mclass Tasks_model extends App_Model[m
         $affectedRows      = 0;[m
         $data['startdate'] = to_sql_date($data['startdate']);[m
         $data['duedate']   = to_sql_date($data['duedate']);[m
[32m+[m[32m        $data['report_document'] = json_encode($data['report_document']);[m
 [m
         $checklistItems = [];[m
         if (isset($data['checklist_items']) && count($data['checklist_items']) > 0) {[m
[1mdiff --git a/application/views/admin/custom_fields/customfield.php b/application/views/admin/custom_fields/customfield.php[m
[1mindex 7aecc03..17b88c0 100644[m
[1m--- a/application/views/admin/custom_fields/customfield.php[m
[1m+++ b/application/views/admin/custom_fields/customfield.php[m
[36m@@ -105,6 +105,9 @@[m
                                 <option value="projects" <?php if (isset($custom_field) && $custom_field->fieldto == 'projects') {[m
                                 echo 'selected';[m
                             } ?>><?php echo _l('projects'); ?></option>[m
[32m+[m[32m                            <option value="reports" <?php if (isset($custom_field) && $custom_field->fieldto == 'reports') {[m
[32m+[m[32m                                echo 'selected';[m
[32m+[m[32m                            } ?>><?php echo _l('Document de raportare'); ?></option>[m
                                 <option value="tickets" <?php if (isset($custom_field) && $custom_field->fieldto == 'tickets') {[m
                                 echo 'selected';[m
                             } ?>><?php echo _l('tickets'); ?></option>[m
[1mdiff --git a/application/views/admin/tasks/task.php b/application/views/admin/tasks/task.php[m
[1mindex ba8379f..1d9dcde 100644[m
[1m--- a/application/views/admin/tasks/task.php[m
[1m+++ b/application/views/admin/tasks/task.php[m
[36m@@ -447,6 +447,13 @@[m
                         </div>[m
                         <?php $rel_id_custom_field = (isset($task) ? $task->id : false); ?>[m
                         <?php echo render_custom_fields('tasks', $rel_id_custom_field); ?>[m
[32m+[m[32m                        <?php $rel_id_custom_field = (isset($task) ? $task->id : false); ?>[m
[32m+[m[32m                        <?php $lists =  $custom_fields = get_custom_fields('reports');[m
[32m+[m[32m                        $selected = [];[m
[32m+[m[32m                        if (isset($task->report_document)) {[m
[32m+[m[32m                            $selected = json_decode($task->report_document);[m
[32m+[m[32m                        }[m
[32m+[m[32m                        echo render_select('report_document[]', $lists, ['id', ['name']], _l('Document de raportare'), $selected, ['multiple' => true, 'data-actions-box' => true], [], '', '', false); ?>[m
                         <hr />[m
                         <p class="bold"><?php echo _l('task_add_edit_description'); ?></p>[m
                         <?php[m
[1mdiff --git a/application/views/admin/tasks/view_task_template.php b/application/views/admin/tasks/view_task_template.php[m
[1mindex 36f8bb1..b238c7f 100644[m
[1m--- a/application/views/admin/tasks/view_task_template.php[m
[1m+++ b/application/views/admin/tasks/view_task_template.php[m
[36m@@ -407,6 +407,7 @@[m
             <div class="clearfix"></div>[m
             <p class="hide text-muted no-margin" id="task-no-checklist-items">[m
                 <?php echo _l('task_no_checklist_items_found'); ?></p>[m
[32m+[m
             <div class="row checklist-items-wrapper">[m
                 <div class="col-md-12 ">[m
                     <div id="checklist-items">[m
[36m@@ -552,6 +553,15 @@[m
                 </div>[m
             </div>[m
             <?php } ?>[m
[32m+[m[41m            [m
[32m+[m[32m            <?php if (!empty($task->report_document)) { ?>[m
[32m+[m[32m            <hr />[m
[32m+[m[32m            <div class="clearfix"></div>[m
[32m+[m[32m            <div>[m
[32m+[m[32m                <p><b>Document de raportare</b></p>[m
[32m+[m[32m                <?php echo render_custom_fields('reports', false, [], [], json_decode($task->report_document)); ?>[m
[32m+[m[32m            </div>[m
[32m+[m[32m            <?php } ?>[m
             <hr />[m
             <a href="#" id="taskCommentSlide" onclick="slideToggle('.tasks-comments'); return false;">[m
                 <h4 class="mbot20 font-medium"><?php echo _l('task_comments'); ?></h4>[m
