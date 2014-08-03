-- 删除decoration不存在的site_decoration
create temporary table orphan_site_decoration select ID from wp_posts where post_type = 'site_decoration' and ID in (select post_id from wp_postmeta where meta_key = 'decoration' and meta_value not in (select ID from wp_posts where post_type = 'decoration'));
update wp_posts set post_status = 'trash' where ID in (select ID from t);

-- 同步decoration的post_date到site_decoration
create temporary table decoration select * from wp_posts where post_type = 'decoration';
update wp_posts inner join wp_postmeta on wp_posts.ID = wp_postmeta.post_id and wp_postmeta.meta_key = 'decoration' inner join decoration on decoration.ID = wp_postmeta.meta_value
set wp_posts.post_date = decoration.post_date, wp_posts.post_date_gmt = decoration.post_date_gmt
where wp_posts.post_type = 'site_decoration';

-- 删除post不存在的meta
delete from wp_postmeta where post_id not in (select ID from wp_posts);

-- 删除user不存在的用户meta
delete from wp_usermeta where user_id not in (select ID from wp_users);

-- 替换半角括号为全角
update wp_postmeta set meta_value = replace(replace(meta_value, '(', '（'), ')', '）') where meta_key = 'frames';
