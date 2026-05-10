<?php
if (!defined('ABSPATH')) exit;

function drycured_id($s){ return preg_replace('/[^A-Za-z0-9\-_]/','',(string)$s); }
function drycured_make_id($title,$source=''){ $slug=strtoupper(preg_replace('/[^A-Z0-9]+/','-',substr(sanitize_title(remove_accents($title)),0,55))); return 'DRY-'.trim($slug,'-').'-'.strtoupper(substr(md5($title.'|'.$source),0,8)); }
function drycured_find_recipe_by_recipe_id($rid){
    $ids=get_posts(['post_type'=>'dry_recipe','post_status'=>['publish','draft','pending','private'],'meta_key'=>'_dry_recipe_id','meta_value'=>drycured_id($rid),'numberposts'=>1,'fields'=>'ids']);
    return $ids ? intval($ids[0]) : 0;
}
function drycured_first_term($post_id,$tax){ $t=get_the_terms($post_id,$tax); return ($t&&!is_wp_error($t))?$t[0]->name:''; }
function drycured_terms($post_id,$tax){ $t=get_the_terms($post_id,$tax); return ($t&&!is_wp_error($t))?array_map(fn($x)=>['name'=>$x->name,'slug'=>$x->slug],$t):[]; }
function drycured_excerpt($s,$n=170){ $s=trim(wp_strip_all_tags($s)); return mb_strlen($s)>$n?mb_substr($s,0,$n-1).'…':$s; }
function drycured_assign_tax($post_id,$tax,$vals){
    $vals=array_filter(array_map('sanitize_text_field',(array)$vals));
    if($vals) wp_set_object_terms($post_id,array_values(array_unique($vals)),$tax,false);
}
function drycured_label($k){
    $map=['dio_mesa'=>'Dio mesa','sastojak'=>'Sastojak','kolicina'=>'Količina','kolicina_za_10_kg'=>'Količina','udio'=>'Udio','uloga'=>'Uloga','napomena'=>'Napomena','problem'=>'Problem','uzrok'=>'Uzrok','rjesenje'=>'Rješenje','priprema'=>'Priprema'];
    return $map[$k] ?? ucfirst(str_replace('_',' ',$k));
}
function drycured_technical_title($title){
    $t=mb_strtolower($title);
    foreach(['arhitektura','plan','audit','workflow','codex','zaključak','qa provjera','stanje korpusa','metodologija','deduplikacij','tehnološki profil','procesni profil','dokument:','kategorija:'] as $bad){
        if(str_contains($t,$bad)) return true;
    }
    return false;
}
function drycured_is_sausage_text($t){ $t=mb_strtolower($t); return str_contains($t,'kobasic')||str_contains($t,'salama')||str_contains($t,'kulen'); }
