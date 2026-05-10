<?php
if (!defined('ABSPATH')) exit;
function drycured_register_recipe_post_types(){
    register_post_type('dry_recipe',[
        'labels'=>['name'=>'Drycured recepti','singular_name'=>'Drycured recept','add_new_item'=>'Dodaj novi recept','edit_item'=>'Uredi recept'],
        'public'=>true,'has_archive'=>true,'rewrite'=>['slug'=>'recepti-baza'],'show_in_rest'=>true,'menu_icon'=>'dashicons-food',
        'supports'=>['title','editor','excerpt','thumbnail','custom-fields'],
    ]);
    register_post_type('dry_recipe_submission',[
        'labels'=>['name'=>'Prijave recepata','singular_name'=>'Prijava recepta'],
        'public'=>false,'show_ui'=>true,'show_in_rest'=>true,'menu_icon'=>'dashicons-clipboard','supports'=>['title','editor','custom-fields'],
    ]);
}
