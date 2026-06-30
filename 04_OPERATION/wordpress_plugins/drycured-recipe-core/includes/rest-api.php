<?php
if (!defined('ABSPATH')) exit;

function drycured_register_recipe_rest_routes(){
    register_rest_route('drycured/v1','/recipes',['methods'=>'GET','callback'=>'drycured_rest_get_recipes','permission_callback'=>'__return_true']);
    register_rest_route('drycured/v1','/recipes/(?P<recipe_id>[A-Za-z0-9\-_]+)',['methods'=>'GET','callback'=>'drycured_rest_get_recipe','permission_callback'=>'__return_true']);
    
    register_rest_route('drycured/v1','/filters/regions',['methods'=>'GET','callback'=>'drycured_rest_get_regions_by_country','permission_callback'=>'__return_true']);
    register_rest_route('drycured/v1','/filters',['methods'=>'GET','callback'=>'drycured_rest_get_filters','permission_callback'=>'__return_true']);
}
function drycured_rest_get_recipes(WP_REST_Request $req){
    nocache_headers();
    $taxq=['relation'=>'AND']; $map=['country'=>'dry_country','region'=>'dry_region','microregion'=>'dry_microregion','category'=>'dry_product_category','type'=>'dry_product_type','meat'=>'dry_meat_type','process'=>'dry_process_type','method'=>'dry_preparation_method'];
    foreach($map as $p=>$tax){ $v=sanitize_text_field($req->get_param($p)); if($v) $taxq[]=['taxonomy'=>$tax,'field'=>'slug','terms'=>sanitize_title(remove_accents($v))]; }
    $args=['post_type'=>'dry_recipe','post_status'=>'publish','posts_per_page'=>min(1200,max(1,intval($req->get_param('per_page')?:1000))),'paged'=>max(1,intval($req->get_param('page')?:1)),'s'=>sanitize_text_field($req->get_param('search')?:''),'orderby'=>'title','order'=>'ASC'];
    if(count($taxq)>1) $args['tax_query']=$taxq;
    $q=new WP_Query($args); $items=[];
    foreach($q->posts as $p) $items[]=drycured_item($p->ID);
    return rest_ensure_response(['items'=>$items,'total'=>intval($q->found_posts),'pages'=>intval($q->max_num_pages),'atlas'=>drycured_atlas($items)]);
}
function drycured_rest_get_recipe(WP_REST_Request $req){ $pid=drycured_find_recipe_by_recipe_id($req['recipe_id']); if(!$pid) return new WP_Error('not_found','Recept nije pronađen.',['status'=>404]); return rest_ensure_response(drycured_item($pid,true)); }
function drycured_item($pid,$full=false){
    $data=json_decode(get_post_meta($pid,'_dry_recipe_data',true),true); if(!is_array($data)) $data=[];
    $item=['id'=>$pid,'recipe_id'=>get_post_meta($pid,'_dry_recipe_id',true),'title'=>get_the_title($pid),'excerpt'=>get_the_excerpt($pid),'link'=>get_permalink($pid),'calculator_ready'=>(bool)get_post_meta($pid,'_dry_calculator_ready',true),'calculator_url'=>home_url('/kalkulator/?recipe_id='.rawurlencode((string)get_post_meta($pid,'_dry_recipe_id',true))),'data'=>$data,'terms'=>drycured_all_terms($pid)];
    $item['country']=$data['country_hr']??$data['country']??drycured_first_term($pid,'dry_country');
    $item['region']=$data['region']??drycured_first_term($pid,'dry_region');
    $item['category']=$data['category']??drycured_first_term($pid,'dry_product_category');
    $item['meat_types']=$data['meat_types']??array_column($item['terms']['dry_meat_type']??[],'name');
    $item['processes']=$data['processes']??array_column($item['terms']['dry_process_type']??[],'name');
    if($full) $item['content']=apply_filters('the_content',get_post_field('post_content',$pid));
    return $item;
}
function drycured_all_terms($pid){ $tax=['dry_country','dry_region','dry_microregion','dry_product_category','dry_product_type','dry_process_type','dry_meat_type','dry_preparation_method','dry_difficulty','dry_recipe_status']; $out=[]; foreach($tax as $t) $out[$t]=drycured_terms($pid,$t); return $out; }
function drycured_rest_get_filters(){ $tax=['dry_country','dry_region','dry_microregion','dry_product_category','dry_product_type','dry_process_type','dry_meat_type','dry_preparation_method','dry_difficulty','dry_recipe_status']; $out=[]; foreach($tax as $t){ $terms=get_terms(['taxonomy'=>$t,'hide_empty'=>false]); $out[$t]=is_wp_error($terms)?[]:array_map(fn($x)=>['name'=>$x->name,'slug'=>$x->slug,'count'=>$x->count],$terms); } return rest_ensure_response($out); }
function drycured_atlas($items){
    $a=[];
    foreach($items as $it){
        $c=$it['country']?:'Neodređena zemlja';
        $r=$it['region']?:'Neodređena regija';
        $cat=$it['category']?:'Ostalo';
        $a[$c]['count']=($a[$c]['count']??0)+1;
        $a[$c]['regions'][$r]['count']=($a[$c]['regions'][$r]['count']??0)+1;
        $a[$c]['regions'][$r]['categories'][$cat]=($a[$c]['regions'][$r]['categories'][$cat]??0)+1;
    }
    // Seed canonical 0-count regions (drycured_canonical_regions_by_country defined in shortcodes.php,
    // available at REST callback time since all plugin files are loaded before hooks fire)
    if (function_exists('drycured_canonical_regions_by_country')) {
        foreach (drycured_canonical_regions_by_country() as $cc => $regs) {
            if (!isset($a[$cc])) $a[$cc] = ['count' => 0, 'regions' => []];
            foreach ($regs as $cr) {
                if (!isset($a[$cc]['regions'][$cr])) $a[$cc]['regions'][$cr] = ['count' => 0, 'categories' => []];
            }
        }
    }
    return $a;
}

function drycured_rest_get_regions_by_country($req){
    $country_slug = sanitize_text_field($req->get_param('country') ?? '');
    if(!$country_slug) return rest_ensure_response([]);
    $args = ['post_type'=>'dry_recipe','post_status'=>'publish','posts_per_page'=>-1,
        'tax_query'=>[['taxonomy'=>'dry_country','field'=>'slug','terms'=>$country_slug]]];
    $posts = get_posts($args);
    $regions = [];
    foreach($posts as $post){
        $terms = get_the_terms($post->ID,'dry_region');
        if($terms && !is_wp_error($terms)){
            foreach($terms as $t){
                if(!isset($regions[$t->term_id]))
                    $regions[$t->term_id]=['name'=>$t->name,'slug'=>$t->slug,'count'=>0];
                $regions[$t->term_id]['count']++;
            }
        }
    }
    usort($regions,fn($a,$b)=>strcmp($a['name'],$b['name']));
    return rest_ensure_response(array_values($regions));
}
