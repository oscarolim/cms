<?php

namespace App\Helpers\Joom;
 
class Forms
{
    public static function checkbox_is_checked($key, $default_value, $stored_value = false)
    {
        return (is_array(old($key)) && in_array($default_value, old($key)) || $stored_value) ? ' checked' : '';
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
}
?>