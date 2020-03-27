<?php // Set up users / Add roles

//Get Current User Info
$current_user_info = wp_get_current_user();
$current_user_id = $current_user_info->ID;
$current_user_roles = get_user_roles($current_user_id);

//Get all roles of current user with word Leader
$current_user_roles_leader = array_contains($current_user_roles, 'Leader');
//Loop through $current_user_roles_leader and add ead_private_posts cap
foreach($current_user_roles_leader as $role){
    add_read_private_posts($role)
}
