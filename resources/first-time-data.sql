insert into `pages` (`page_name`, `logo_name`, `page_title`) values ('Comtech Page', 'Comtech', 'Comtech');
insert into `roles` (`role_name`, `role_id`) values ('admin', '99');
insert into `roles` (`role_name`, `role_id`) values ('user', '0');
insert into `users` (`name`, `username`,`password`, `page_id`, `role_id`) values ('Teo', 'teo', '$2y$10$brRcXuccK3x0dlIZu1adZOz15KQk8dY/I6DKK6x2GWU9p.jcCJOCC', '1', '1');