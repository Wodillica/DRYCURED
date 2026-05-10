<?php
if (!defined('ABSPATH')) exit;
function drycured_register_recipe_meta(){
    foreach(['_dry_recipe_id'=>'string','_dry_recipe_data'=>'string'] as $key=>$type){
        register_post_meta('dry_recipe',$key,['type'=>$type,'single'=>true,'show_in_rest'=>true,'sanitize_callback'=>'wp_kses_post','auth_callback'=>fn()=>current_user_can('edit_posts')]);
    }
    register_post_meta('dry_recipe','_dry_calculator_ready',['type'=>'boolean','single'=>true,'show_in_rest'=>true,'default'=>false,'auth_callback'=>fn()=>current_user_can('edit_posts')]);
    register_post_meta('dry_recipe','_dry_public_ready',['type'=>'boolean','single'=>true,'show_in_rest'=>true,'default'=>true,'auth_callback'=>fn()=>current_user_can('edit_posts')]);
}
