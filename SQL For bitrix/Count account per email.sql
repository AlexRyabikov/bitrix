/*Query selects users where account per email > 1*/
select
email AS 'Почта',
LOGIN AS 'Логин', 
name AS 'Имя', 
last_name AS 'Фамилия', 
last_login AS 'Последняя авторизация', 
date_register AS 'Дата регистрации'
from b_user where email in (select email from b_user group by email having count(*) > 1)
ORDER BY email DESC
