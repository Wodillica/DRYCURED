<?php
if (!defined('ABSPATH')) exit;
function drycured_register_recipe_taxonomies(){
    $tax=[
        'dry_country'=>['Zemlje','Zemlja'],'dry_region'=>['Regije','Regija'],'dry_microregion'=>['Mikroregije','Mikroregija'],
        'dry_product_category'=>['Kategorije proizvoda','Kategorija proizvoda'],'dry_product_type'=>['Tipovi proizvoda','Tip proizvoda'],
        'dry_process_type'=>['Postupci','Postupak'],'dry_meat_type'=>['Vrste mesa','Vrsta mesa'],'dry_preparation_method'=>['Načini pripreme','Način pripreme'],
        'dry_difficulty'=>['Težine izrade','Težina izrade'],'dry_recipe_status'=>['Statusi recepata','Status recepta']
    ];
    foreach($tax as $slug=>$lab){
        register_taxonomy($slug,['dry_recipe'],[
            'labels'=>['name'=>$lab[0],'singular_name'=>$lab[1]],'public'=>true,'hierarchical'=>true,'show_in_rest'=>true,'show_admin_column'=>true,
            'rewrite'=>['slug'=>str_replace('_','-',$slug)]
        ]);
    }
}
