set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming','delaystopped','incorporate','report');

ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming','delaystopped','incorporate','report');
