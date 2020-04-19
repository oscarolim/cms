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
        if($structure == NULL)
            return;
        $html = '';
        foreach(json_decode($structure, true) as $block)
        {
            $html_header_buttons = '<button class="btn btn-primary btn-sm" onclick="content_block_save(\''.$block['id'].'\')">Save</button>';
            $html_header_buttons .= '<button class="btn btn-danger btn-sm ml-2" onclick="remove_content_block(\''.$block['id'].'\')">Delete</button>';

            switch($block['type'])
            {
                case 'text':
                    $block_text_content = $content->where('block_id', $block['id'])->first();
                    $settings = $block_text_content != NULL ? json_decode($block_text_content->block_settings, TRUE) : ['border' => 'none'];
                    $html .= '<div class="content-block" data-block_id="'.$block['id'].'" data-block_type="text" id="'.$block['id'].'-container">';
                    $html .= '<div class="cb-header"><span class="cb-title">Text</span><div class="float-right text-align-right">'.$html_header_buttons.'</div></div>';
                    $html .= '<input type="hidden" id="'.$block['id'].'-type" value="text" />';
                    $html .= '<input type="hidden" id="'.$block['id'].'-new" value="'.($block_text_content == NULL ? 'yes' : 'no').'" />';
                    $html .= '<div class="form-group"><label for="'.$block['id'].'-border">Border</label>';
                    $html .= '<select id="'.$block['id'].'-border" class="form-control">';
                    $html .= '<option value="none" '.($settings['border'] ?? '' == 'none' ? 'selected' : '').'>None</value>';
                    $html .= '<option value="top" '.($settings['border'] ?? '' == 'top' ? 'selected' : '').'>Top</value>';
                    $html .= '</select></div>';
                    $html .= '<textarea class="form-control ckeditor" rows="5" name="' .$block['id'] .'-text" id="'.$block['id'].'-text">'.($block_text_content->block_content ?? '').'</textarea>';
                    $html .= '</div>';
                break;
                case 'image':
                    $block_image_content = $content->where('block_id', $block['id'])->first();
                    $image = $block_image_content != NULL && $block_image_content->file != NULL ? $block_image_content->file->where('id', $block_image_content->block_content)->first() : NULL;
                    $html .= '<div class="content-block" data-block_id="'.$block['id'].'" data-block_type="image" id="'.$block['id'].'-container">';
                    $html .= '<div class="cb-header"><span class="cb-title">Image</span><div class="float-right text-align-right">'.$html_header_buttons.'</div></div>';
                    $html .= '<input type="hidden" id="'.$block['id'].'-type" value="image" />';
                    $html .= '<input type="hidden" id="'.$block['id'].'-new" value="'.($block_image_content == NULL ? 'yes' : 'no').'" />';
                    $html .= '<div class="image-preview" id="'.$block['id'].'-image-preview">'.($image != NULL ? '<img src="'.asset($image->folder.$image->filename).'" alt="'.$image->name.'">' : '').'</div>';
                    $html .= '<input type="hidden" id="'.$block['id'].'-file_id" value="'.($image != NULL ? $image->id : 0).'" />';
                    $html .= '<div class="upload-form-container" data-id="'.$block['id'].'"></div>';
                    $html .= '</div>';
                break;
                case 'text+image':
                    $block_text_content = $content->where('block_id', $block['id'])->where('block_tag', 'text')->first();
                    $block_image_content = $content->where('block_id', $block['id'])->where('block_tag', 'image')->first();
                    $settings = $block_image_content != NULL ? json_decode($block_image_content->block_settings, TRUE) : ['position' => 'left'];
                    $image = $block_image_content != NULL && $block_image_content->file != NULL ? $block_image_content->file->where('id', $block_image_content->block_content)->first() : NULL;
                    $html .= '<div class="content-block" data-block_id="'.$block['id'].'" data-block_type="text+image" id="'.$block['id'].'-container">';
                    $html .= '<div class="cb-header"><span class="cb-title">Text + Image</span><div class="float-right text-align-right">'.$html_header_buttons.'</div></div>';
                    $html .= '<input type="hidden" id="'.$block['id'].'-type" value="text+image" />';
                    $html .= '<input type="hidden" id="'.$block['id'].'-new" value="'.($block_text_content == NULL && $block_image_content == NULL ? 'yes' : 'no').'" />';
                    $html .= '<div class="form-group"><label for="'.$block['id'].'-image-position">Image position</label>';
                    $html .= '<select id="'.$block['id'].'-image-position" class="form-control">';
                    $html .= '<option value="left" '.($settings['position'] == 'left' ? 'selected' : '').'>Left</value>';
                    $html .= '<option value="right" '.($settings['position'] == 'right' ? 'selected' : '').'>Right</value>';
                    $html .= '<option value="full" '.($settings['position'] == 'full' ? 'selected' : '').'>Full width (text over image, centred)</value>';
                    $html .= '</select></div>';
                    $html .= '<div class="form-group"><textarea class="form-control ckeditor" rows="5" name="' .$block['id'] .'-text" id="'.$block['id'].'-text">'.($block_text_content->block_content ?? '').'</textarea></div>';
                    $html .= '<div class="image-preview" id="'.$block['id'].'-image-preview">'.($image != NULL ? '<img src="'.asset($image->folder.$image->filename).'" alt="'.$image->name.'">' : '').'</div>';
                    $html .= '<input type="hidden" id="'.$block['id'].'-file_id" value="'.($image != NULL ? $image->id : 0).'" />';
                    $html .= '<div class="upload-form-container" data-id="'.$block['id'].'"></div>';
                    $html .= '</div>';
                break;
                case 'text+video':
                    $block_text_content = $content->where('block_id', $block['id'])->where('block_tag', 'text')->first();
                    $block_video_content = $content->where('block_id', $block['id'])->where('block_tag', 'video')->first();
                    $settings = $block_video_content != NULL ? json_decode($block_video_content->block_settings, TRUE) : ['position' => 'left'];
                    $html .= '<div class="content-block" data-block_id="'.$block['id'].'" data-block_type="text+video" id="'.$block['id'].'-container">';
                    $html .= '<div class="cb-header"><span class="cb-title">Text + Video</span><div class="float-right text-align-right">'.$html_header_buttons.'</div></div>';
                    $html .= '<input type="hidden" id="'.$block['id'].'-type" value="text+video" />';
                    $html .= '<input type="hidden" id="'.$block['id'].'-new" value="'.($block_text_content == NULL && $block_video_content == NULL ? 'yes' : 'no').'" />';
                    $html .= '<div class="form-group"><label for="'.$block['id'].'-video-position">Video position</label>';
                    $html .= '<select id="'.$block['id'].'-video-position" class="form-control">';
                    $html .= '<option value="left" '.($settings['position'] == 'left' ? 'selected' : '').'>Left</value>';
                    $html .= '<option value="right" '.($settings['position'] == 'right' ? 'selected' : '').'>Right</value>';
                    $html .= '<option value="top" '.($settings['position'] == 'top' ? 'selected' : '').'>Top</value>';
                    $html .= '<option value="bottom" '.($settings['position'] == 'bottom' ? 'selected' : '').'>Bottom</value>';
                    $html .= '</select></div>';
                    $html .= '<div class="form-group"><label for="'.$block['id'].'-text">Text</label><textarea class="form-control ckeditor" rows="5" name="' .$block['id'] .'-text" id="'.$block['id'].'-text">'.($block_text_content->block_content ?? '').'</textarea></div>';
                    $html .= '<div class="form-group"><label for="'.$block['id'].'-video_url">Video embed link</label><input type="text" class="form-control" name="' .$block['id'] .'-video_url" id="'.$block['id'] .'-video_url" value="'.($block_video_content->block_content ?? '').'" /></div>';
                    $html .= '</div>';
                break;
            }
        }
        return $html;
    }
}
?>