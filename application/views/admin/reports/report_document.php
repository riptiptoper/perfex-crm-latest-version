<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <fieldset>
                            <legend><h4>Custom Field Groups</h4></legend>
                            <a href="#"
                                class="btn btn-primary pull-left display-block mright5" data-toggle="modal" data-target="#addGroupModal">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('Add Group'); ?>
                            </a>
                            <!-- Add Modal -->
                            <div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <form id="addDocumentGroupForm">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Group</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                                <div class="form-group row">
                                                    <label for="GroupName" class="col-sm-2 col-form-label"></label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="name" class="form-control" id="GroupName" placeholder="Group name">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php $lists = get_custom_fields('reports'); ?>
                                                    <select class="selectpicker form-control" name="value[]" multiple aria-label="Default select example" data-live-search="true">
                                                        <?php foreach($lists as $list) { ?>
                                                            <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Add</button>
                                        </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Update Modal -->
                            <div class="modal fade" id="updateGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <form id="updateDocumentGroupForm">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Edit Group</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                                <input id="group_id" name="group_id" class="form-control" type="hidden">
                                                <div class="form-group row">
                                                    <label for="GroupName" class="col-sm-2 col-form-label"></label>
                                                    <div class="col-sm-12">
                                                        <input type="text" id="group_name" name="name" class="form-control" id="GroupName" placeholder="Group name">
                                                    </div>
                                                </div>
                                                <div >
                                                    <select id="group_val" class="selectpicker form-control" name="value[]" multiple>
                                                    </select>
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </fieldset>
                        <br>
                        <div >
                            <table class="table table-bordered">
                                <thead>
                                    <th><h5>Group Name</h5></th>
                                    <th><h5>Custom fields</h5></th>
                                    <th><h5>Actions</h5></th>
                                </thead>
                                <tbody>
                                    <?php foreach($groups as $group) { ?>
                                    <tr>
                                        <td width="1%"><?php echo $group['name']; ?></td>
                                        <td width="4%">
                                            <?php 
                                                foreach(json_decode($group['value']) as $id)
                                                {
                                                    $this->db->where('id', $id);
                                                    $result = $this->db->get(db_prefix() . 'customfields')->row();
                                                    echo $result->name . ', ';
                                                }
                                            ?>
                                        </td>
                                        <td width="1%">
                                            <button class="btn btn-success" onclick="editGroup(<?php echo $group['id'];?>)" style="margin-right: 10px">Edit</button>
                                            <button class="btn btn-danger" onclick="deleteGroup(<?php echo $group['id'];?>)">Delete</button>
                                        </td>
                                    </tr>
                                    <?php } ?>        
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div>
                            <?php foreach($groups as $group) { ?>
                            <h4><?php echo $group['name']; ?></h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('task'); ?></th>
                                        <?php 
                                        foreach(json_decode($group['value']) as $id)
                                        {
                                            $this->db->where('id', $id);
                                            $this->db->order_by('id', 'asc');
                                            $result = $this->db->get(db_prefix() . 'customfields')->row();
                                            echo '<th>' . $result->name . '</th>';
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $this->db->where('group_id', $group['id']);
                                        $this->db->order_by('fieldid', 'asc');
                                        $custom_field_values = $this->db->get(db_prefix() . 'customfieldsvalues')->result_array();
                                        $task_ids = [];
                                        foreach($custom_field_values as $val)
                                        {
                                            if (in_array($val['relid'], $task_ids))
                                                continue;
                                            array_push($task_ids, $val['relid']);
                                        }

                                        foreach($task_ids as $task_id)
                                        {
                                            echo '<tr>';
                                            $this->db->where('id', $task_id);
                                            $task = $this->db->get(db_prefix() . 'tasks')->row();
                                            echo '<td>' . $task->name . '</td>';
                                            foreach(json_decode($group['value']) as $val)
                                            {
                                                $this->db->where('group_id', $group['id']);
                                                $this->db->where('relid', $task_id);
                                                $this->db->where('fieldid', $val);
                                                $value = $this->db->get(db_prefix() . 'customfieldsvalues')->row();
                                                if ($value)
                                                    echo '<td>' . $value->value . '</td>';
                                                else
                                                    echo '<td>---</td>';
                                            }
                                            echo '</tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$('#addDocumentGroupForm').on('submit', function(event) {
    event.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: admin_url + 'reports/add_group',
        method: "POST",
        data: formData,
        success: function(response) {
            if (response == 1) {
                $('#addGroupModal').modal('hide');
                window.location.reload();
            }
        },
    });
});

$('#updateDocumentGroupForm').on('submit', function(event) {
    event.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: admin_url + 'reports/edit_group',
        method: "POST",
        data: formData,
        success: function(response) {
            if (response == 1) {
                $('#updateGroupModal').modal('hide');
                window.location.reload();
            }
        },
    });
});

function deleteGroup(id)
{
    $.ajax({
        url: admin_url + 'reports/delete_group',
        method: "get",
        data: {'id': id},
        success: function(response) {
            window.location.reload();
        },
    });
}

function editGroup(id)
{
    $.ajax({
        url: admin_url + 'reports/update_group',
        method: "get",
        data: {'id': id},
        success: function(response) {
            var data = JSON.parse(response);
            var group = data['group'];
            $('#group_name').val(group.name);
            $('#group_id').val(group.id);
            $('#group_val').html(data['html']);
            $('#group_val').selectpicker("refresh");
            $('#updateGroupModal').modal('show');
        },
    });
}

function change_expense_report_year(year) {
    window.location.href = admin_url + 'reports/expenses_vs_income/' + year;
}
</script>
</body>

</html>