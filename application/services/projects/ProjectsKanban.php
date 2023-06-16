<?php

namespace app\services\projects;

use app\services\AbstractKanban;

class ProjectsKanban extends AbstractKanban
{
    protected function table()
    {
        return 'projects';
    }

    public function defaultSortDirection()
    {
        return 'ASC';
    }

    public function defaultSortColumn()
    {
        return '';
    }

    public function limit()
    {
        return get_option('leads_kanban_limit');
    }

    protected function applySearchQuery($q)
    {
        $q = $this->ci->db->escape_like_str($q);
        $this->ci->db->where('(' . db_prefix() . 'projects.name LIKE "%' . $q . '%" ESCAPE \'!\'  OR ' . db_prefix() . 'projects.description LIKE "%' . $q . '%" ESCAPE \'!\'  OR ' . db_prefix() . 'clients.company LIKE "%' . $q . '%" ESCAPE \'!\')');
        
        return $this;
    }

    protected function initiateQuery()
    {
        $this->ci->db->select('*, ' . db_prefix() . 'clients.company as client_company');
        $this->ci->db->from('projects');
        $this->ci->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'projects.clientid', 'left');
        $this->ci->db->where('status', $this->status);
       
        return $this;
    }
}
