<?php

namespace App\Helpers\Joom;

use Illuminate\Support\Facades\DB;

class Forms
{
    public static function checkbox_is_checked($key, $default_value, $stored_value = false)
    {
        return (is_array(old($key)) && in_array($default_value, old($key)) || $stored_value) ? ' checked' : '';
    }

    public static function option_is_selected($key, $option_value, $stored_value = false)
    {
        return (old($key) == $option_value || $stored_value == $option_value) ? ' selected' : '';
    }

    public static function table_rows($data, Array $values, Array $actions, $actions_route, $children_key = '', $depth = 0)
    {
        $row = '';
        foreach($data as $entry)
        {
            $row .= '<tr>';
            foreach($values as $index => $value)
               $row .= '<td>'.($index == 0 && $depth > 0 ? str_repeat('-', $depth).'&nbsp;' : '').$entry->$value.'</td>';
            $row .= '<td class="text-right">'.self::table_actions($actions, $actions_route, $entry).'</td>';
            $row .='</tr>';

            if($children_key != NULL)
                $row .= self::table_rows($entry->$children_key, $values, $actions, $actions_route, $children_key, $depth + 1);
        }

        return $row;
    }

    public static function table_actions($actions, $actions_route, $item)
    {
        $html = '';
        foreach($actions as $action)
        {
            switch($action)
            {
                case 'move':
                    $html .= '<a class="btn btn-success btn-sm ml-2 table-action-move" href="#" data-action="'.$actions_route.'/'.$item->id.'/move/up">&uarr;</a>';
                    $html .= '<a class="btn btn-success btn-sm ml-2 table-action-move" href="#" data-action="'.$actions_route.'/'.$item->id.'/move/down">&darr;</a>';
                break;
                case 'published':
                    $html .= isset($item->published) ? '<a class="btn '.($item->published ? 'btn-dark' : 'btn-outline-dark').' btn-sm ml-2 table-action-published" href="#" data-action="'.$actions_route.'/'.$item->id.'/published">'.($item->published ? 'published' : 'draft').'</a>' : '';
                break;

                case 'edit':
                    $html .= '<a class="btn btn-info btn-sm ml-2" href="'.$actions_route.'/'.$item->id.'/edit">edit</a>';
                break;

                case 'delete':
                    $html .= '<a class="btn btn-danger btn-sm ml-2 table-action-delete" href="#" data-action="'.$actions_route.'/'.$item->id.'">Delete</a>';
                break;
            }
        }
        return $html;
    }

    public static function get_next_order($table, $where = '')
    {
        $order = DB::select('SELECT MAX(position) AS max_order FROM '.$table.($where != '' ? ' WHERE '.$where : ''));
        $order = $order[0]->max_order;
        if($order == NULL || $order == 0)
            return 1;
        else
            return ($order + 1);
    }

    public static function move($table, $direction, $item_id, $where = '')
    {
        $prop_id = 'id';
        $prop_order = 'position';
        $prop_deleted = 'deleted_at';

        $current_item = DB::select('SELECT * FROM '.$table.' WHERE '.$prop_id.' = '.$item_id);
        switch($direction)
        {
            case 'down':
                $other_item = DB::select('SELECT * FROM '.$table.' WHERE '.$prop_order.' > '.$current_item[0]->$prop_order.' AND '.$prop_deleted.' IS NULL'.($where != '' ? ' AND '.$where : '').' ORDER BY '.$prop_order.' ASC LIMIT 1');
                break;
            case 'up':
                $other_item = DB::select('SELECT * FROM '.$table.' WHERE '.$prop_order.' < '.$current_item[0]->$prop_order.' AND '.$prop_deleted.' IS NULL'.($where != '' ? ' AND '.$where : '').' ORDER BY '.$prop_order.' DESC LIMIT 1');
                break;
                
            /*case 'first':
                $current_item = $this->item_details($table, $field_prefix, $item_id);
                $where[$prop_deleted] = 0;
                $first_item = $this->db->select('')->from($table)->where($where)->order_by($prop_order.' DESC')->limit(1)->get();
                $first_item = array_shift($first_item->result());
                
                //Decrease order value and send item to first place
                $this->db->query('UPDATE '.$table.' SET '.$prop_order.' = '.$prop_order.' - 1 WHERE '.$prop_order.' > '.$current_item->$prop_order);
                $this->db->update($table, array($prop_order => $first_item->$prop_order), array($prop_id => $current_item->$prop_id));
                
                return TRUE;
                break;*/
        }
        
        if(count($other_item) == 1)
        {
            DB::update('UPDATE '.$table.' SET '.$prop_order.' = ? WHERE '.$prop_id.' = ?', [$current_item[0]->$prop_order, $other_item[0]->$prop_id]);
            DB::update('UPDATE '.$table.' SET '.$prop_order.' = ? WHERE '.$prop_id.' = ?', [$other_item[0]->$prop_order, $current_item[0]->$prop_id]);
            return TRUE;
        }
        else
            return FALSE;
    }

    public static function parse_block_configuration($structure, $content)
    {
        $html = '';
        foreach(json_decode($structure, true) as $block)
        {
            $block_content = $content->where('block_id', $block['id'])->first();
            switch($block['type'])
            {
                case 'text':
                    $html .= '<div class="content-block" data-block_id="'.$block['id'].'" data-block_type="text" id="'.$block['id'].'-container">';
                    $html .= '<div class="cb-header"><span class="cb-title">Text</span><div class="float-right text-align-right">';
                    $html .= '<button class="btn btn-primary btn-sm" onclick="content_block_save(\''.$block['id'].'\')">Save</button>';
                    $html .= '<button class="btn btn-danger btn-sm ml-2" onclick="remove_content_block(\''.$block['id'].'\')">Delete</button></div></div>';
                    $html .= '<input type="hidden" id="'.$block['id'].'-type" value="text" />';
                    $html .= '<input type="hidden" id="'.$block['id'].'-new" value="'.($block_content == NULL ? 'yes' : 'no').'" />';
                    $html .= '<textarea class="form-control ckeditor" rows="5" name="' .$block['id'] .'-text" id="'.$block['id'].'-text">'.($block_content->block_content ?? '').'</textarea>';
                    $html .= '</div>';
                break;
                case 'image':
                break;
                case 'text+image':
                break;
            }
        }
        return $html;
    }
}
?>