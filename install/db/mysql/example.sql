INSERT INTO f_events (ID, EVENT_TYPE, NAME, ADDITIONAL_PROPS, CONDITIONS, SORT) VALUES (1, 1, 'Зарегистрировался на сайте', 'N;', 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:5:"False";}s:8:"CHILDREN";a:5:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBElement";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:4;}}i:1;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:29;}}i:2;a:2:{s:8:"CLASS_ID";s:12:"CondIBIBlock";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:6;}}i:3;a:2:{s:8:"CLASS_ID";s:8:"CondUser";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:20;}}i:4;a:2:{s:8:"CLASS_ID";s:13:"CondUserGroup";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2;}}}}', 1) ;
INSERT INTO f_events (ID, EVENT_TYPE, NAME, ADDITIONAL_PROPS, CONDITIONS, SORT) VALUES (2, 0, 'Авторизовался на сайте', '', '', 2) ;
INSERT INTO f_events (ID, NAME, ADDITIONAL_PROPS, SORT) VALUES (3, 0, 'Заполнена информация о себе', '', '', 3) ;
INSERT INTO f_events (ID, NAME, ADDITIONAL_PROPS, SORT) VALUES (4, 4, 'Добавление рецепта', 'a:1:{s:9:"IBLOCK_ID";s:1:"5";}', 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:0:{}}', 4) ;
INSERT INTO f_events (ID, NAME, ADDITIONAL_PROPS, SORT) VALUES (5, 3, 'ereh', 'a:1:{s:10:"FORM_FIELD";a:1:{i:0;s:1:"1";}}', '', 10) ;
INSERT INTO f_events (ID, NAME, ADDITIONAL_PROPS, SORT) VALUES (6, 3, 'sgwegew', 'a:1:{s:10:"FORM_FIELD";a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}}', '', 10) ;

INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (1, 'REGISTRATION', 'Перед регистрацией пользователя', 'USER', 'OnBeforeUserRegisterHandler', 1) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (2, 'REGISTRATION', 'После регистрации пользователя', 'USER', 'OnAfterUserRegisterHandler', 2) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (3, 'AUTHORIZATION', 'Перед авторизацией пользователя', 'USER', 'OnBeforeUserLoginHandler', 3) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (4, 'AUTHORIZATION', 'После авторизации пользователя', 'USER', 'OnAfterUserLoginHandler', 4) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (5, 'EDIT_PROFILE', 'Перед редактированием пользователя', 'USER', 'OnBeforeUserUpdateHandler', 5) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (6, 'EDIT_PROFILE', 'После редактирования пользователя', 'USER', 'OnAfterUserUpdateHandler', 6) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (7, 'IBLOCK_ELEMENT_ADD', 'После добавления элемента инфоблока', 'IBLOCK', 'OnAfterIBlockElementAddHandler', 7) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (8, 'IBLOCK_ELEMENT_ADD', 'Перед добавлением элемента инфоблока', 'IBLOCK', 'OnBeforeIBlockElementAddHandler', 7) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (9, 'IBLOCK_ELEMENT_UPDATE', 'После редактирования элемента инфоблока', 'IBLOCK', 'OnAfterIBlockElementUpdateHandler', 7) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (10, 'IBLOCK_ELEMENT_UPDATE', 'Перед редактированием элемента инфоблока', 'IBLOCK', 'OnBeforeIBlockElementUpdateHandler', 7) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (11, 'IBLOCK_ELEMENT_DELETE', 'После удаления элемента инфоблока', 'IBLOCK', 'OnAfterIBlockElementDeleteHandler', 7) ;
INSERT INTO f_event_types (ID, CODE, NAME, TYPE, HANDLER, SORT) VALUES (12, 'IBLOCK_ELEMENT_DELETE', 'Перед удалением элемента инфоблока', 'IBLOCK', 'OnBeforeIBlockElementDeleteHandler', 7) ;


INSERT INTO f_conditions (ID, NAME, EVENT_ID, ACTION_ID, SORT) VALUES (1, 'Тестовое условие 1', 1, 1, 1) ;
INSERT INTO f_conditions (ID, NAME, EVENT_ID, ACTION_ID, SORT) VALUES (2, 'Тестовое условие 2', 2, 2, 2) ;
INSERT INTO f_conditions (ID, NAME, EVENT_ID, ACTION_ID, SORT) VALUES (3, 'Тестовое условие 3', 3, 3, 3) ;

INSERT INTO f_actions (ID, NAME, ACTION_TYPE, ADDITIONAL_PROPS, BODY_PARAMS, SORT) VALUES (1, 'Присвоить бейдж Первый рецепт', 1, '', '', 1) ;
INSERT INTO f_actions (ID, NAME, ACTION_TYPE, ADDITIONAL_PROPS, BODY_PARAMS, SORT) VALUES (2, 'Присвоить баллы за рецепт', 2, '', '', 2) ;
INSERT INTO f_actions (ID, NAME, ACTION_TYPE, ADDITIONAL_PROPS, BODY_PARAMS, SORT) VALUES (3, 'Тестовое действие 3', 3, '', '', 3) ;

INSERT INTO f_action_types (ID, NAME, CODE, SORT) VALUES (1, 'Присвоить бейдж', 'BAGE', 1) ;
INSERT INTO f_action_types (ID, NAME, CODE, SORT) VALUES (2, 'Присвоить баллы', 'ADD_POINTS', 2) ;
INSERT INTO f_action_types (ID, NAME, CODE, SORT) VALUES (3, 'Отправить письмо', 'SEND_MAIL', 3) ;

INSERT INTO f_triggers (ID, NAME, EVENT_ID, CONDITION_ID, ACTION_ID, SORT) VALUES (1, 'Тестовое триггер 1', 1, 1, 1, 1) ;
INSERT INTO f_triggers (ID, NAME, EVENT_ID, CONDITION_ID, ACTION_ID, SORT) VALUES (2, 'Тестовое триггер 2', 2, 2, 2, 2) ;
INSERT INTO f_triggers (ID, NAME, EVENT_ID, CONDITION_ID, ACTION_ID, SORT) VALUES (3, 'Тестовое триггер 3', 3, 3, 3, 3) ;

INSERT INTO f_event_type_fields (ID, NAME, TYPE, FIELD_TYPE, SORT) VALUES (1, 'Группа пользователя', 1, "GROUP_ID", 1) ;
INSERT INTO f_event_type_fields (ID, NAME, TYPE, FIELD_TYPE, SORT) VALUES (2, 'Группа пользователя', 2, "GROUP_ID", 2) ;
INSERT INTO f_event_type_fields (ID, NAME, TYPE, FIELD_TYPE, SORT) VALUES (3, 'Заполнение поля', 3, "FORM_FIELD", 3) ;
INSERT INTO f_event_type_fields (ID, NAME, TYPE, FIELD_TYPE, SORT) VALUES (4, 'Инфоболок', 4, "IBLOCK_ID", 4) ;
INSERT INTO f_event_type_fields (ID, NAME, TYPE, FIELD_TYPE, SORT) VALUES (5, 'Элемент инфоблока', 5, "IBLOCK_ELEMENT", 5) ;

INSERT INTO f_triggers_log (ID, NAME, TRIGGER_ID, DATE_CREATE, CREATED_BY) VALUES (1, "Тестовое событие", 2, "20013-02-11 11:09:21", 1) ;