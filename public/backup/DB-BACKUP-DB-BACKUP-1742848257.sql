DROP TABLE IF EXISTS branches;

CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `contact_email` varchar(191) DEFAULT NULL,
  `contact_phone` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `descriptions` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS charge_limits;

CREATE TABLE `charge_limits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `minimum_amount` decimal(18,2) NOT NULL,
  `maximum_amount` decimal(18,2) NOT NULL,
  `fixed_charge` decimal(10,2) NOT NULL,
  `charge_in_percentage` decimal(10,2) NOT NULL,
  `gateway_id` bigint(20) NOT NULL,
  `gateway_type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO charge_limits VALUES('1','10.00','50000.00','0.00','2.00','1','App\\Models\\WithdrawMethod','2025-02-03 17:17:53','2025-02-14 08:33:57');
INSERT INTO charge_limits VALUES('2','100.00','100000.00','0.00','1.00','2','App\\Models\\WithdrawMethod','2025-02-06 18:36:56','2025-02-14 08:33:34');



DROP TABLE IF EXISTS currency;

CREATE TABLE `currency` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(3) NOT NULL,
  `exchange_rate` decimal(10,6) NOT NULL,
  `base_currency` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO currency VALUES('1','GHS','1.000000','1','1','','2025-01-30 09:48:52');



DROP TABLE IF EXISTS custom_fields;

CREATE TABLE `custom_fields` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(191) NOT NULL,
  `field_type` varchar(20) NOT NULL,
  `default_value` text DEFAULT NULL,
  `field_width` varchar(30) NOT NULL,
  `max_size` int(11) DEFAULT NULL,
  `is_required` varchar(191) NOT NULL DEFAULT 'nullable',
  `table` varchar(30) NOT NULL,
  `allow_for_signup` tinyint(4) NOT NULL DEFAULT 0,
  `allow_to_list_view` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `order` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS database_backups;

CREATE TABLE `database_backups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file` varchar(191) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS deposit_methods;

CREATE TABLE `deposit_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `currency_id` bigint(20) NOT NULL,
  `minimum_amount` decimal(10,2) NOT NULL,
  `maximum_amount` decimal(10,2) NOT NULL,
  `fixed_charge` decimal(10,2) NOT NULL,
  `charge_in_percentage` decimal(10,2) NOT NULL,
  `descriptions` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `requirements` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS deposit_requests;

CREATE TABLE `deposit_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `method_id` bigint(20) unsigned NOT NULL,
  `credit_account_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `converted_amount` decimal(10,2) NOT NULL,
  `charge` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `attachment` varchar(191) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `transaction_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deposit_requests_member_id_foreign` (`member_id`),
  KEY `deposit_requests_method_id_foreign` (`method_id`),
  KEY `deposit_requests_credit_account_id_foreign` (`credit_account_id`),
  CONSTRAINT `deposit_requests_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `savings_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deposit_requests_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deposit_requests_method_id_foreign` FOREIGN KEY (`method_id`) REFERENCES `deposit_methods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS email_sms_templates;

CREATE TABLE `email_sms_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `subject` varchar(191) NOT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `notification_body` text DEFAULT NULL,
  `shortcode` text DEFAULT NULL,
  `email_status` tinyint(4) NOT NULL DEFAULT 0,
  `sms_status` tinyint(4) NOT NULL DEFAULT 0,
  `notification_status` tinyint(4) NOT NULL DEFAULT 0,
  `template_mode` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = all, 1 = email, 2 = sms, 3 = notification',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO email_sms_templates VALUES('1','Transfer Money','TRANSFER_MONEY','Transfer Money','<div>
<div>Dear {{name}},</div>
<div>You have received {{amount}} to {{account_number}} from {{sender_account_number}} on {{dateTime}}</div>
</div>','Dear {{name}}, You have received {{amount}} to {{account_number}} from {{sender_account_number}} on {{dateTime}}','Dear {{name}}, You have received {{amount}} to {{account_number}} from {{sender_account_number}} on {{dateTime}}','{{name}} {{account_number}} {{amount}} {{sender}} {{sender_account_number}} {{balance}} {{dateTime}}','0','0','0','0','','');
INSERT INTO email_sms_templates VALUES('2','Deposit Money','DEPOSIT_MONEY','Deposit Money','<div>
<div>Dear {{name}},</div>
<div>Your account has been credited with {{amount}} on {{dateTime}}</div>
</div>','Dear {{name}}, Your account has been credited with {{amount}} on {{dateTime}}','Dear {{name}}, Your account has been credited with {{amount}} on {{dateTime}}','{{name}} {{account_number}} {{amount}} {{dateTime}} {{balance}} {{depositMethod}}','0','0','0','0','','');
INSERT INTO email_sms_templates VALUES('3','Deposit Request Approved','DEPOSIT_REQUEST_APPROVED','Deposit Request Approved','<div>
<div>Dear {{name}},</div>
<div>Your deposit request has been approved. Your account {{account_number}} has been credited with {{amount}} on {{dateTime}}</div>
</div>','Dear {{name}}, Your deposit request has been approved. Your account {{account_number}} has been credited with {{amount}} on {{dateTime}}','Dear {{name}}, Your deposit request has been approved. Your account {{account_number}} has been credited with {{amount}} on {{dateTime}}','{{name}} {{account_number}} {{amount}} {{dateTime}} {{balance}} {{depositMethod}}','0','0','0','0','','');
INSERT INTO email_sms_templates VALUES('4','Loan Request Approved','LOAN_REQUEST_APPROVED','Loan Request Approved','<div>
<div>Dear {{name}},</div>
<div>Your loan request of {{amount}} has been approved on {{dateTime}}</div>
</div>','Dear {{name}}, Your loan request of {{amount}} has been approved on {{dateTime}}','Dear {{name}}, Your loan request of {{amount}} has been approved on {{dateTime}}','{{name}} {{amount}} {{dateTime}}','0','0','0','0','','');
INSERT INTO email_sms_templates VALUES('5','Withdraw Request Approved','WITHDRAW_REQUEST_APPROVED','Withdraw Request Approved','<div>
<div>Dear {{name}},</div>
<div>Your withdraw request has been approved. Your account has been debited with {{amount}} on {{dateTime}}</div>
</div>','Dear {{name}}, Your withdraw request has been approved. Your account has been debited with {{amount}} on {{dateTime}}','Dear {{name}}, Your withdraw request has been approved. Your account has been debited with {{amount}} on {{dateTime}}','{{name}} {{account_number}} {{amount}} {{withdrawMethod}} {{balance}} {{dateTime}}','0','0','0','0','','');
INSERT INTO email_sms_templates VALUES('6','Deposit Request Rejected','DEPOSIT_REQUEST_REJECTED','Deposit Request Rejected','<div>
<div>Dear {{name}},</div>
<div>Your deposit request of {{amount}} has been rejected.</div>
<div>&nbsp;</div>
<div>Amount:&nbsp;{{amount}}</div>
<div>Deposit Method: {{depositMethod}}</div>
</div>','Dear {{name}}, Your deposit request of {{amount}} has been rejected.','Dear {{name}}, Your deposit request of {{amount}} has been rejected.','{{name}}  {{account_number}} {{amount}} {{depositMethod}} {{balance}}','0','0','0','0','','');
INSERT INTO email_sms_templates VALUES('7','Loan Request Rejected','LOAN_REQUEST_REJECTED','Loan Request Rejected','<div>
<div>Dear {{name}},</div>
<div>Your loan request of {{amount}} has been rejected on {{dateTime}}</div>
</div>','Dear {{name}}, Your loan request of {{amount}} has been rejected on {{dateTime}}','Dear {{name}}, Your loan request of {{amount}} has been rejected on {{dateTime}}','{{name}} {{amount}} {{dateTime}}','0','0','0','0','','');
INSERT INTO email_sms_templates VALUES('8','Withdraw Request Rejected','WITHDRAW_REQUEST_REJECTED','Withdraw Request Rejected','<div>
<div>Dear {{name}}, Your withdraw request has been rejected. Your transferred amount {{amount}} has returned back to your account.</div>
</div>','Dear {{name}}, Your withdraw request has been rejected. Your transferred amount {{amount}} has returned back to your account.','Dear {{name}}, Your withdraw request has been rejected. Your transferred amount {{amount}} has returned back to your account.','{{name}} {{account_number}} {{amount}} {{withdrawMethod}} {{dateTime}} {{balance}}','0','0','0','0','','');
INSERT INTO email_sms_templates VALUES('9','Withdraw Money','WITHDRAW_MONEY','Withdraw Money','<div>
<div>Dear {{name}},</div>
<div>Your account has been debited with {{amount}} on {{dateTime}}</div>
</div>','Dear {{name}}, Your account has been debited with {{amount}} on {{dateTime}}','Dear {{name}}, Your account has been debited with {{amount}} on {{dateTime}}','{{name}} {{account_number}} {{amount}} {{dateTime}} {{withdrawMethod}} {{balance}}','0','0','0','0','','');
INSERT INTO email_sms_templates VALUES('10','Member Request Accepted','MEMBER_REQUEST_ACCEPTED','Member Request Accepted','<div>
<div>Dear {{name}},</div>
<div>Your member request has been accepted by authority on {{dateTime}}. You can now login to your account by using your email and password.</div>
</div>','','','{{name}} {{member_no}} {{dateTime}}','0','0','0','1','','');



DROP TABLE IF EXISTS expense_categories;

CREATE TABLE `expense_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `color` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO expense_categories VALUES('1','Light Bill','Yellow','','2025-02-14 08:24:58','2025-02-14 08:24:58');



DROP TABLE IF EXISTS expenses;

CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_date` datetime NOT NULL,
  `expense_category_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(191) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `attachment` varchar(191) DEFAULT NULL,
  `created_user_id` bigint(20) DEFAULT NULL,
  `updated_user_id` bigint(20) DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  KEY `expenses_branch_id_foreign` (`branch_id`),
  CONSTRAINT `expenses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO expenses VALUES('1','2025-02-14 08:25:00','1','100.00','haewddjwhdh','','1739521567signature-1-removebg-preview.png','1','','','2025-02-14 08:26:07','2025-02-14 08:26:07');
INSERT INTO expenses VALUES('2','2025-03-16 22:27:00','1','500.00','jshhq1233','March light bill','','1','','','2025-03-16 22:28:30','2025-03-16 22:28:30');



DROP TABLE IF EXISTS failed_jobs;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS group_members;

CREATE TABLE `group_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) unsigned NOT NULL,
  `member_id` bigint(20) unsigned NOT NULL,
  `total_contributed` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payout_position_number` int(11) NOT NULL,
  `has_received_payout` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Yes | 0 = No',
  `amount_received` decimal(10,2) NOT NULL DEFAULT 0.00,
  `has_received_payout_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `savings_account_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_members_group_id_foreign` (`group_id`),
  KEY `group_members_member_id_foreign` (`member_id`),
  CONSTRAINT `group_members_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `group_members_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO group_members VALUES('11','3','2','100.00','1','0','0.00','','2025-03-22 14:15:10','2025-03-22 18:05:40','2');
INSERT INTO group_members VALUES('12','1','2','100.00','1','0','0.00','','2025-03-22 16:17:45','2025-03-22 17:40:26','2');
INSERT INTO group_members VALUES('14','3','3','200.00','2','0','0.00','','2025-03-22 18:04:46','2025-03-22 18:05:40','4');
INSERT INTO group_members VALUES('15','1','2','100.00','2','0','0.00','','2025-03-23 22:02:05','2025-03-23 22:02:05','10');
INSERT INTO group_members VALUES('16','3','1','100.00','3','0','0.00','','2025-03-23 22:05:18','2025-03-23 22:05:18','3');
INSERT INTO group_members VALUES('17','3','2','200.00','4','0','0.00','','2025-03-23 22:05:45','2025-03-23 22:05:45','10');
INSERT INTO group_members VALUES('18','1','1','200.00','3','0','0.00','','2025-03-24 04:22:47','2025-03-24 04:22:47','11');
INSERT INTO group_members VALUES('19','1','1','100.00','4','0','0.00','','2025-03-24 04:27:01','2025-03-24 04:27:01','3');



DROP TABLE IF EXISTS groups;

CREATE TABLE `groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(191) NOT NULL,
  `member_limit` int(11) DEFAULT NULL COMMENT 'Maximum number of members allowed in the group',
  `target_amount` decimal(15,2) DEFAULT NULL COMMENT 'Target amount for group savings',
  `monthly_contribution` decimal(10,2) NOT NULL,
  `total_members` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL COMMENT '1 = Active | 0 = Inactive',
  `created_user_id` bigint(20) DEFAULT NULL,
  `updated_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO groups VALUES('1','Group A','10','2000.00','500.00','4','1','1','','2025-02-02 07:33:08','2025-03-24 04:27:01');
INSERT INTO groups VALUES('3','Group B','5','2800.00','700.00','4','1','1','','2025-02-03 14:54:00','2025-03-23 22:05:45');



DROP TABLE IF EXISTS guarantors;

CREATE TABLE `guarantors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) unsigned NOT NULL,
  `member_id` bigint(20) unsigned NOT NULL,
  `savings_account_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guarantors_loan_id_foreign` (`loan_id`),
  KEY `guarantors_member_id_foreign` (`member_id`),
  CONSTRAINT `guarantors_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guarantors_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS interest_posting;

CREATE TABLE `interest_posting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_type_id` bigint(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS loan_collaterals;

CREATE TABLE `loan_collaterals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `collateral_type` varchar(191) NOT NULL,
  `serial_number` varchar(191) DEFAULT NULL,
  `estimated_price` decimal(10,2) NOT NULL,
  `attachments` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_collaterals_loan_id_foreign` (`loan_id`),
  CONSTRAINT `loan_collaterals_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS loan_payments;

CREATE TABLE `loan_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) unsigned NOT NULL,
  `paid_at` date NOT NULL,
  `late_penalties` decimal(10,2) NOT NULL,
  `interest` decimal(10,2) NOT NULL,
  `repayment_amount` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `member_id` bigint(20) unsigned NOT NULL,
  `transaction_id` bigint(20) DEFAULT NULL,
  `repayment_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_payments_loan_id_foreign` (`loan_id`),
  KEY `loan_payments_member_id_foreign` (`member_id`),
  CONSTRAINT `loan_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loan_payments_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS loan_products;

CREATE TABLE `loan_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `loan_id_prefix` varchar(10) DEFAULT NULL,
  `starting_loan_id` bigint(20) DEFAULT NULL,
  `minimum_amount` decimal(10,2) NOT NULL,
  `maximum_amount` decimal(10,2) NOT NULL,
  `late_payment_penalties` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `interest_rate` decimal(10,2) NOT NULL,
  `interest_type` varchar(191) NOT NULL,
  `term` int(11) NOT NULL,
  `term_period` varchar(15) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `loan_application_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `loan_application_fee_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = Fixed | 1 = Percentage',
  `loan_processing_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `loan_processing_fee_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = Fixed | 1 = Percentage',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS loan_repayments;

CREATE TABLE `loan_repayments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) NOT NULL,
  `repayment_date` date NOT NULL,
  `amount_to_pay` decimal(10,2) NOT NULL,
  `penalty` decimal(10,2) NOT NULL,
  `principal_amount` decimal(10,2) NOT NULL,
  `interest` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS loans;

CREATE TABLE `loans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` varchar(30) DEFAULT NULL,
  `loan_product_id` bigint(20) unsigned NOT NULL,
  `borrower_id` bigint(20) unsigned NOT NULL,
  `debit_account_id` bigint(20) unsigned DEFAULT NULL,
  `first_payment_date` date NOT NULL,
  `release_date` date DEFAULT NULL,
  `currency_id` bigint(20) NOT NULL,
  `applied_amount` decimal(10,2) NOT NULL,
  `total_payable` decimal(10,2) DEFAULT NULL,
  `total_paid` decimal(10,2) DEFAULT NULL,
  `late_payment_penalties` decimal(10,2) NOT NULL,
  `attachment` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `approved_date` date DEFAULT NULL,
  `approved_user_id` bigint(20) DEFAULT NULL,
  `created_user_id` bigint(20) DEFAULT NULL,
  `updated_user_id` bigint(20) DEFAULT NULL,
  `branch_id` bigint(20) DEFAULT NULL,
  `custom_fields` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS member_documents;

CREATE TABLE `member_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `document` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_documents_member_id_foreign` (`member_id`),
  CONSTRAINT `member_documents_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS members;

CREATE TABLE `members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `branch_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `email` varchar(100) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `business_name` varchar(100) DEFAULT NULL,
  `member_no` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `zip` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `credit_source` varchar(191) DEFAULT NULL,
  `photo` varchar(191) DEFAULT NULL,
  `custom_fields` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO members VALUES('1','Rafick','Mustapha','','5','1','rafick@savings.com','233','207728823','','20251001','male','Tamale','','','Dakpema Road, NL-12','','1742680400rafick.jpg','[]','2025-01-30 10:19:48','2025-03-22 21:53:20');
INSERT INTO members VALUES('2','Abdul','Rauf','','','1','rauf@savings.com','233','543214796','','20251002','male','Tamale','','','Dakpema Road, NL-12','','default.png','[]','2025-02-02 08:01:42','2025-02-02 08:01:42');
INSERT INTO members VALUES('3','John','Doe','','','1','john@savings.com','233','0244383735','','20251003','male','Accra','Accra','','','','default.png','[]','2025-02-06 00:53:01','2025-02-06 00:53:01');



DROP TABLE IF EXISTS migrations;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations VALUES('1','2014_10_12_000000_create_users_table','1');
INSERT INTO migrations VALUES('2','2014_10_12_100000_create_password_resets_table','1');
INSERT INTO migrations VALUES('3','2019_08_19_000000_create_failed_jobs_table','1');
INSERT INTO migrations VALUES('4','2019_09_01_080940_create_settings_table','1');
INSERT INTO migrations VALUES('5','2019_12_14_000001_create_personal_access_tokens_table','1');
INSERT INTO migrations VALUES('6','2020_07_02_145857_create_database_backups_table','1');
INSERT INTO migrations VALUES('7','2020_07_06_142817_create_roles_table','1');
INSERT INTO migrations VALUES('8','2020_07_06_143240_create_permissions_table','1');
INSERT INTO migrations VALUES('9','2021_03_22_071324_create_setting_translations','1');
INSERT INTO migrations VALUES('10','2021_07_02_145504_create_pages_table','1');
INSERT INTO migrations VALUES('11','2021_07_02_145952_create_page_translations_table','1');
INSERT INTO migrations VALUES('12','2021_08_06_104648_create_branches_table','1');
INSERT INTO migrations VALUES('13','2021_08_07_111236_create_currency_table','1');
INSERT INTO migrations VALUES('14','2021_08_08_132702_create_payment_gateways_table','1');
INSERT INTO migrations VALUES('15','2021_08_08_152535_create_deposit_methods_table','1');
INSERT INTO migrations VALUES('16','2021_08_08_164152_create_withdraw_methods_table','1');
INSERT INTO migrations VALUES('17','2021_08_31_201125_create_navigations_table','1');
INSERT INTO migrations VALUES('18','2021_08_31_201126_create_navigation_items_table','1');
INSERT INTO migrations VALUES('19','2021_08_31_201127_create_navigation_item_translations_table','1');
INSERT INTO migrations VALUES('20','2021_10_22_070458_create_email_sms_templates_table','1');
INSERT INTO migrations VALUES('21','2022_03_21_075342_create_members_table','1');
INSERT INTO migrations VALUES('22','2022_03_24_090932_create_member_documents_table','1');
INSERT INTO migrations VALUES('23','2022_03_28_114203_create_savings_products_table','1');
INSERT INTO migrations VALUES('24','2022_04_13_073108_create_savings_accounts_table','1');
INSERT INTO migrations VALUES('25','2022_04_13_073109_create_transactions_table','1');
INSERT INTO migrations VALUES('26','2022_05_31_074804_create_expense_categories_table','1');
INSERT INTO migrations VALUES('27','2022_05_31_074918_create_expenses_table','1');
INSERT INTO migrations VALUES('28','2022_06_01_082019_create_loan_products_table','1');
INSERT INTO migrations VALUES('29','2022_06_01_083021_create_loans_table','1');
INSERT INTO migrations VALUES('30','2022_06_01_083022_create_loan_collaterals_table','1');
INSERT INTO migrations VALUES('31','2022_06_01_083025_create_loan_payments_table','1');
INSERT INTO migrations VALUES('32','2022_06_01_083069_create_loan_repayments_table','1');
INSERT INTO migrations VALUES('33','2022_06_06_072245_create_guarantors_table','1');
INSERT INTO migrations VALUES('34','2022_07_26_155338_create_deposit_requests_table','1');
INSERT INTO migrations VALUES('35','2022_07_26_163427_create_withdraw_requests_table','1');
INSERT INTO migrations VALUES('36','2022_08_09_160105_create_notifications_table','1');
INSERT INTO migrations VALUES('37','2022_08_15_055625_create_interest_posting_table','1');
INSERT INTO migrations VALUES('38','2022_08_27_151317_create_transaction_categories_table','1');
INSERT INTO migrations VALUES('39','2022_08_29_102757_create_schedule_tasks_histories_table','1');
INSERT INTO migrations VALUES('40','2022_09_13_162539_add_branch_id_to_users_table','1');
INSERT INTO migrations VALUES('41','2022_09_18_074806_add_branch_id_to_expenses_table','1');
INSERT INTO migrations VALUES('42','2022_10_16_081858_add_charge_to_deposit_requests_table','1');
INSERT INTO migrations VALUES('43','2022_10_29_095023_add_status_to_members_table','1');
INSERT INTO migrations VALUES('44','2023_01_29_093731_create_charge_limits_table','1');
INSERT INTO migrations VALUES('45','2024_02_18_171623_add_auto_account_number_to_savings_products_table','1');
INSERT INTO migrations VALUES('46','2024_05_10_205624_add_starting_loan_id_to_loan_products_table','1');
INSERT INTO migrations VALUES('47','2024_05_11_175920_create_custom_fields_table','1');
INSERT INTO migrations VALUES('48','2024_05_15_183254_add_custom_fields_to_loans_table','1');
INSERT INTO migrations VALUES('49','2024_05_15_201559_add_2fa_columns_to_users_table','1');
INSERT INTO migrations VALUES('50','2024_09_09_171047_add_loan_application_fee_to_loan_products_table','1');
INSERT INTO migrations VALUES('51','2025_01_30_222422_create_groups_table','2');
INSERT INTO migrations VALUES('52','2025_01_30_222449_create_group_members_table','2');
INSERT INTO migrations VALUES('53','2025_02_14_070211_add_collector_id_to_transactions_table','3');
INSERT INTO migrations VALUES('54','2025_03_16_111950_add_savings_account_id_to_group_members_table','4');
INSERT INTO migrations VALUES('55','2025_03_21_055624_add_member_limit_and_target_amount_to_groups_table','4');



DROP TABLE IF EXISTS navigation_item_translations;

CREATE TABLE `navigation_item_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `navigation_item_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `navigation_item_translations_navigation_item_id_locale_unique` (`navigation_item_id`,`locale`),
  CONSTRAINT `navigation_item_translations_navigation_item_id_foreign` FOREIGN KEY (`navigation_item_id`) REFERENCES `navigation_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS navigation_items;

CREATE TABLE `navigation_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `navigation_id` bigint(20) unsigned NOT NULL,
  `type` varchar(20) NOT NULL,
  `page_id` bigint(20) unsigned DEFAULT NULL,
  `url` varchar(191) DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `target` varchar(191) NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `position` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `css_class` varchar(191) DEFAULT NULL,
  `css_id` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `navigation_items_parent_id_foreign` (`parent_id`),
  KEY `navigation_items_page_id_foreign` (`page_id`),
  KEY `navigation_items_navigation_id_index` (`navigation_id`),
  CONSTRAINT `navigation_items_navigation_id_foreign` FOREIGN KEY (`navigation_id`) REFERENCES `navigations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `navigation_items_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `navigation_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `navigation_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS navigations;

CREATE TABLE `navigations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS notifications;

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS page_translations;

CREATE TABLE `page_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(191) NOT NULL,
  `title` text NOT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_translations_page_id_locale_unique` (`page_id`,`locale`),
  CONSTRAINT `page_translations_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS pages;

CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS password_resets;

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS payment_gateways;

CREATE TABLE `payment_gateways` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `slug` varchar(30) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `is_crypto` tinyint(4) NOT NULL DEFAULT 0,
  `parameters` text DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `supported_currencies` text DEFAULT NULL,
  `extra` text DEFAULT NULL,
  `exchange_rate` decimal(10,6) DEFAULT NULL,
  `fixed_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `charge_in_percentage` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `maximum_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO payment_gateways VALUES('1','PayPal','PayPal','paypal.png','0','0','{\"client_id\":\"\",\"client_secret\":\"\",\"environment\":\"sandbox\"}','','{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"USD\"}','','','0.00','0.00','0.00','0.00','','');
INSERT INTO payment_gateways VALUES('2','Stripe','Stripe','stripe.png','0','0','{\"secret_key\":\"\",\"publishable_key\":\"\"}','','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}','','','0.00','0.00','0.00','0.00','','');
INSERT INTO payment_gateways VALUES('3','Razorpay','Razorpay','razorpay.png','0','0','{\"razorpay_key_id\":\"\",\"razorpay_key_secret\":\"\"}','','{\"INR\":\"INR\"}','','','0.00','0.00','0.00','0.00','','');
INSERT INTO payment_gateways VALUES('4','Paystack','Paystack','paystack.png','0','0','{\"paystack_public_key\":\"\",\"paystack_secret_key\":\"\"}','','{\"GHS\":\"GHS\",\"NGN\":\"NGN\",\"ZAR\":\"ZAR\"}','','','0.00','0.00','0.00','0.00','','');
INSERT INTO payment_gateways VALUES('5','BlockChain','BlockChain','blockchain.png','0','1','{\"blockchain_api_key\":\"\",\"blockchain_xpub\":\"\"}','','{\"BTC\":\"BTC\"}','','','0.00','0.00','0.00','0.00','','');
INSERT INTO payment_gateways VALUES('6','Flutterwave','Flutterwave','flutterwave.png','0','0','{\"public_key\":\"\",\"secret_key\":\"\",\"encryption_key\":\"\",\"environment\":\"sandbox\"}','','{\"BIF\":\"BIF\",\"CAD\":\"CAD\",\"CDF\":\"CDF\",\"CVE\":\"CVE\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"GHS\":\"GHS\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"KES\":\"KES\",\"LRD\":\"LRD\",\"MWK\":\"MWK\",\"MZN\":\"MZN\",\"NGN\":\"NGN\",\"RWF\":\"RWF\",\"SLL\":\"SLL\",\"STD\":\"STD\",\"TZS\":\"TZS\",\"UGX\":\"UGX\",\"USD\":\"USD\",\"XAF\":\"XAF\",\"XOF\":\"XOF\",\"ZMK\":\"ZMK\",\"ZMW\":\"ZMW\",\"ZWD\":\"ZWD\"}','','','0.00','0.00','0.00','0.00','','');
INSERT INTO payment_gateways VALUES('7','VoguePay','VoguePay','VoguePay.png','1','0','{\"merchant_id\":\"\"}','','{\"USD\":\"USD\",\"GBP\":\"GBP\",\"EUR\":\"EUR\",\"GHS\":\"GHS\",\"NGN\":\"NGN\",\"ZAR\":\"ZAR\"}','','','0.00','0.00','0.00','0.00','','');
INSERT INTO payment_gateways VALUES('8','Mollie','Mollie','Mollie.png','1','0','{\"api_key\":\"\"}','','{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}','','','0.00','0.00','0.00','0.00','','');
INSERT INTO payment_gateways VALUES('9','CoinPayments','CoinPayments','CoinPayments.png','1','1','{\"public_key\":\"\",\"private_key\":\"\",\"merchant_id\":\"\",\"ipn_secret\":\"\"}','','{\"BTC\":\"Bitcoin\",\"BTC.LN\":\"Bitcoin (Lightning Network)\",\"LTC\":\"Litecoin\",\"CPS\":\"CPS Coin\",\"VLX\":\"Velas\",\"APL\":\"Apollo\",\"AYA\":\"Aryacoin\",\"BAD\":\"Badcoin\",\"BCD\":\"Bitcoin Diamond\",\"BCH\":\"Bitcoin Cash\",\"BCN\":\"Bytecoin\",\"BEAM\":\"BEAM\",\"BITB\":\"Bean Cash\",\"BLK\":\"BlackCoin\",\"BSV\":\"Bitcoin SV\",\"BTAD\":\"Bitcoin Adult\",\"BTG\":\"Bitcoin Gold\",\"BTT\":\"BitTorrent\",\"CLOAK\":\"CloakCoin\",\"CLUB\":\"ClubCoin\",\"CRW\":\"Crown\",\"CRYP\":\"CrypticCoin\",\"CRYT\":\"CryTrExCoin\",\"CURE\":\"CureCoin\",\"DASH\":\"DASH\",\"DCR\":\"Decred\",\"DEV\":\"DeviantCoin\",\"DGB\":\"DigiByte\",\"DOGE\":\"Dogecoin\",\"EBST\":\"eBoost\",\"EOS\":\"EOS\",\"ETC\":\"Ether Classic\",\"ETH\":\"Ethereum\",\"ETN\":\"Electroneum\",\"EUNO\":\"EUNO\",\"EXP\":\"EXP\",\"Expanse\":\"Expanse\",\"FLASH\":\"FLASH\",\"GAME\":\"GameCredits\",\"GLC\":\"Goldcoin\",\"GRS\":\"Groestlcoin\",\"KMD\":\"Komodo\",\"LOKI\":\"LOKI\",\"LSK\":\"LSK\",\"MAID\":\"MaidSafeCoin\",\"MUE\":\"MonetaryUnit\",\"NAV\":\"NAV Coin\",\"NEO\":\"NEO\",\"NMC\":\"Namecoin\",\"NVST\":\"NVO Token\",\"NXT\":\"NXT\",\"OMNI\":\"OMNI\",\"PINK\":\"PinkCoin\",\"PIVX\":\"PIVX\",\"POT\":\"PotCoin\",\"PPC\":\"Peercoin\",\"PROC\":\"ProCurrency\",\"PURA\":\"PURA\",\"QTUM\":\"QTUM\",\"RES\":\"Resistance\",\"RVN\":\"Ravencoin\",\"RVR\":\"RevolutionVR\",\"SBD\":\"Steem Dollars\",\"SMART\":\"SmartCash\",\"SOXAX\":\"SOXAX\",\"STEEM\":\"STEEM\",\"STRAT\":\"STRAT\",\"SYS\":\"Syscoin\",\"TPAY\":\"TokenPay\",\"TRIGGERS\":\"Triggers\",\"TRX\":\" TRON\",\"UBQ\":\"Ubiq\",\"UNIT\":\"UniversalCurrency\",\"USDT\":\"Tether USD (Omni Layer)\",\"VTC\":\"Vertcoin\",\"WAVES\":\"Waves\",\"XEM\":\"NEM\",\"XMR\":\"Monero\",\"XSN\":\"Stakenet\",\"XSR\":\"SucreCoin\",\"XVG\":\"VERGE\",\"XZC\":\"ZCoin\",\"ZEC\":\"ZCash\",\"ZEN\":\"Horizen\"}','','','0.00','0.00','0.00','0.00','','');
INSERT INTO payment_gateways VALUES('10','Instamojo','Instamojo','instamojo.png','1','0','{\"api_key\":\"\",\"auth_token\":\"\",\"salt\":\"\",\"environment\":\"sandbox\"}','','{\"INR\":\"INR\"}','','','0.00','0.00','0.00','0.00','','');



DROP TABLE IF EXISTS permissions;

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL,
  `permission` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=338 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions VALUES('164','1','dashboard.total_customer_widget','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('165','1','dashboard.deposit_requests_widget','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('166','1','dashboard.withdraw_requests_widget','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('167','1','dashboard.loan_requests_widget','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('168','1','dashboard.expense_overview_widget','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('169','1','dashboard.deposit_withdraw_analytics','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('170','1','dashboard.recent_transaction_widget','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('171','1','dashboard.due_loan_list','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('172','1','dashboard.active_loan_balances','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('173','1','members.import','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('174','1','members.accept_request','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('175','1','members.reject_request','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('176','1','members.pending_requests','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('177','1','members.send_email','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('178','1','members.send_sms','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('179','1','members.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('180','1','members.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('181','1','members.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('182','1','members.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('183','1','members.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('184','1','custom_fields.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('185','1','custom_fields.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('186','1','custom_fields.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('187','1','custom_fields.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('188','1','member_documents.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('189','1','member_documents.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('190','1','member_documents.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('191','1','member_documents.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('192','1','savings_accounts.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('193','1','savings_accounts.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('194','1','savings_accounts.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('195','1','savings_accounts.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('196','1','savings_accounts.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('197','1','groups.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('198','1','groups.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('199','1','groups.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('200','1','groups.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('201','1','groups.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('202','1','group_members.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('203','1','group_members.updatePayoutStatus','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('204','1','group_members.switchPayoutPosition','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('205','1','group_members.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('206','1','group_members.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('207','1','interest_calculation.calculator','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('208','1','interest_calculation.interest_posting','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('209','1','transactions.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('210','1','transactions.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('211','1','transactions.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('212','1','transactions.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('213','1','transactions.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('214','1','deposit_requests.approve','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('215','1','deposit_requests.reject','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('216','1','deposit_requests.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('217','1','deposit_requests.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('218','1','deposit_requests.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('219','1','withdraw_requests.approve','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('220','1','withdraw_requests.reject','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('221','1','withdraw_requests.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('222','1','withdraw_requests.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('223','1','withdraw_requests.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('224','1','expenses.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('225','1','expenses.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('226','1','expenses.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('227','1','expenses.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('228','1','expenses.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('229','1','loans.admin_calculator','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('230','1','loans.calculate','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('231','1','loans.approve','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('232','1','loans.reject','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('233','1','loans.filter','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('234','1','loans.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('235','1','loans.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('236','1','loans.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('237','1','loans.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('238','1','loans.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('239','1','loan_collaterals.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('240','1','loan_collaterals.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('241','1','loan_collaterals.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('242','1','loan_collaterals.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('243','1','loan_collaterals.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('244','1','guarantors.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('245','1','guarantors.edit','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('246','1','guarantors.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('247','1','loan_payments.index','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('248','1','loan_payments.create','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('249','1','loan_payments.show','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('250','1','loan_payments.destroy','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('251','1','reports.account_statement','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('252','1','reports.account_balances','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('253','1','reports.transactions_report','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('254','1','reports.loan_report','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('255','1','reports.loan_due_report','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('256','1','reports.loan_repayment_report','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('257','1','reports.expense_report','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('258','1','reports.revenue_report','2025-02-05 22:47:02','2025-02-05 22:47:02');
INSERT INTO permissions VALUES('259','2','dashboard.total_customer_widget','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('260','2','dashboard.deposit_requests_widget','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('261','2','dashboard.withdraw_requests_widget','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('262','2','dashboard.expense_overview_widget','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('263','2','dashboard.deposit_withdraw_analytics','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('264','2','dashboard.recent_transaction_widget','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('265','2','members.import','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('266','2','members.accept_request','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('267','2','members.reject_request','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('268','2','members.pending_requests','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('269','2','members.send_email','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('270','2','members.send_sms','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('271','2','members.index','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('272','2','members.create','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('273','2','members.show','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('274','2','members.edit','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('275','2','members.destroy','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('276','2','member_documents.index','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('277','2','member_documents.create','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('278','2','member_documents.edit','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('279','2','member_documents.destroy','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('280','2','savings_accounts.index','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('281','2','savings_accounts.create','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('282','2','savings_accounts.show','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('283','2','savings_accounts.edit','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('284','2','savings_accounts.destroy','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('285','2','groups.index','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('286','2','groups.create','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('287','2','groups.show','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('288','2','groups.edit','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('289','2','groups.destroy','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('290','2','group_members.index','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('291','2','group_members.updatePayoutStatus','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('292','2','group_members.switchPayoutPosition','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('293','2','group_members.edit','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('294','2','group_members.destroy','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('295','2','interest_calculation.calculator','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('296','2','interest_calculation.interest_posting','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('297','2','transactions.index','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('298','2','transactions.create','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('299','2','transactions.show','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('300','2','transactions.edit','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('301','2','transactions.destroy','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('302','2','deposit_requests.approve','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('303','2','deposit_requests.reject','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('304','2','deposit_requests.destroy','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('305','2','deposit_requests.show','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('306','2','deposit_requests.index','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('307','2','withdraw_requests.approve','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('308','2','withdraw_requests.reject','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('309','2','withdraw_requests.destroy','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('310','2','withdraw_requests.show','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('311','2','withdraw_requests.index','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('312','2','expenses.index','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('313','2','expenses.create','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('314','2','expenses.show','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('315','2','expenses.edit','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('316','2','expenses.destroy','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('317','2','reports.account_statement','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('318','2','reports.account_balances','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('319','2','reports.transactions_report','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('320','2','reports.expense_report','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('321','2','reports.revenue_report','2025-02-05 22:49:03','2025-02-05 22:49:03');
INSERT INTO permissions VALUES('322','3','dashboard.total_customer_widget','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('323','3','dashboard.deposit_requests_widget','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('324','3','dashboard.withdraw_requests_widget','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('325','3','dashboard.deposit_withdraw_analytics','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('326','3','dashboard.recent_transaction_widget','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('327','3','members.index','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('328','3','members.show','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('329','3','member_documents.index','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('330','3','savings_accounts.index','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('331','3','savings_accounts.show','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('332','3','groups.index','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('333','3','groups.show','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('334','3','group_members.index','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('335','3','transactions.index','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('336','3','transactions.create','2025-02-05 22:50:20','2025-02-05 22:50:20');
INSERT INTO permissions VALUES('337','3','transactions.show','2025-02-05 22:50:20','2025-02-05 22:50:20');



DROP TABLE IF EXISTS personal_access_tokens;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS roles;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles VALUES('1','Admin','Has Full Access','2025-01-30 09:51:19','2025-01-30 09:51:19');
INSERT INTO roles VALUES('2','Manager','','2025-01-30 09:52:57','2025-01-30 09:52:57');
INSERT INTO roles VALUES('3','Staff','','2025-01-30 10:04:25','2025-01-30 10:04:25');
INSERT INTO roles VALUES('4','User','','2025-02-03 17:21:21','2025-02-03 17:21:21');
INSERT INTO roles VALUES('5','Collector','','2025-02-14 07:11:30','2025-02-14 07:11:30');



DROP TABLE IF EXISTS savings_accounts;

CREATE TABLE `savings_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_number` varchar(30) NOT NULL,
  `member_id` bigint(20) unsigned NOT NULL,
  `savings_product_id` bigint(20) unsigned NOT NULL,
  `status` int(11) NOT NULL COMMENT '1 = action | 2 = Deactivate',
  `opening_balance` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_user_id` bigint(20) DEFAULT NULL,
  `updated_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `savings_accounts_member_id_foreign` (`member_id`),
  KEY `savings_accounts_savings_product_id_foreign` (`savings_product_id`),
  CONSTRAINT `savings_accounts_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `savings_accounts_savings_product_id_foreign` FOREIGN KEY (`savings_product_id`) REFERENCES `savings_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO savings_accounts VALUES('1','IS1001','1','1','1','100.00','','1','','2025-01-30 10:30:58','2025-01-30 10:30:58');
INSERT INTO savings_accounts VALUES('2','GS2001','2','2','1','100.00','','1','','2025-02-02 08:28:31','2025-02-02 08:28:31');
INSERT INTO savings_accounts VALUES('3','GS2002','1','2','1','250.00','','1','','2025-02-02 22:22:53','2025-02-02 22:22:53');
INSERT INTO savings_accounts VALUES('4','GS2003','3','2','1','200.00','initial deposit','1','','2025-02-14 08:17:40','2025-02-14 08:17:40');
INSERT INTO savings_accounts VALUES('10','GS2009','2','2','1','100.00','Initial Group Depo','1','','2025-03-23 22:02:05','2025-03-23 22:02:05');
INSERT INTO savings_accounts VALUES('11','GS2010','1','2','1','200.00','','1','','2025-03-24 04:22:47','2025-03-24 04:22:47');



DROP TABLE IF EXISTS savings_products;

CREATE TABLE `savings_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `account_number_prefix` varchar(10) DEFAULT NULL,
  `starting_account_number` bigint(20) DEFAULT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `interest_rate` decimal(8,2) DEFAULT NULL,
  `interest_method` varchar(30) DEFAULT NULL,
  `interest_period` int(11) DEFAULT NULL,
  `interest_posting_period` int(11) DEFAULT NULL,
  `min_bal_interest_rate` decimal(10,2) DEFAULT NULL,
  `allow_withdraw` tinyint(4) NOT NULL DEFAULT 1,
  `minimum_account_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_deposit_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `maintenance_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `maintenance_fee_posting_period` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '1 = active | 2 = Deactivate',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `savings_products_currency_id_foreign` (`currency_id`),
  CONSTRAINT `savings_products_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO savings_products VALUES('1','Individual Savings','IS','1002','1','','daily_outstanding_balance','','','','1','0.00','0.00','0.00','','1','2025-01-30 10:28:24','2025-01-30 10:30:58');
INSERT INTO savings_products VALUES('2','Group Savings','GS','2011','1','','daily_outstanding_balance','','','','1','0.00','0.00','0.00','','1','2025-01-30 10:29:43','2025-03-24 04:22:47');



DROP TABLE IF EXISTS schedule_tasks_histories;

CREATE TABLE `schedule_tasks_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `others` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS setting_translations;

CREATE TABLE `setting_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setting_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(191) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_translations_setting_id_locale_unique` (`setting_id`,`locale`),
  CONSTRAINT `setting_translations_setting_id_foreign` FOREIGN KEY (`setting_id`) REFERENCES `settings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS settings;

CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `value` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings VALUES('1','mail_type','smtp','','');
INSERT INTO settings VALUES('2','backend_direction','ltr','','2025-01-30 07:04:39');
INSERT INTO settings VALUES('3','language','English','','2025-01-30 14:32:16');
INSERT INTO settings VALUES('4','email_verification','disabled','','');
INSERT INTO settings VALUES('5','allow_singup','yes','','');
INSERT INTO settings VALUES('6','starting_member_no','20251004','','2025-02-06 00:53:01');
INSERT INTO settings VALUES('7','company_name','Bushara Soungsim Susu Enterprise','2025-01-30 06:11:01','2025-01-30 14:32:16');
INSERT INTO settings VALUES('8','site_title','BUSHARA SOUNGSIM SUSU','2025-01-30 06:11:01','2025-01-30 14:32:16');
INSERT INTO settings VALUES('9','phone','0207728823','2025-01-30 06:11:01','2025-01-30 14:32:16');
INSERT INTO settings VALUES('10','email','mustapharafick3@gmail.com','2025-01-30 06:11:01','2025-01-30 14:32:16');
INSERT INTO settings VALUES('11','timezone','Africa/Accra','2025-01-30 06:11:01','2025-01-30 14:32:16');
INSERT INTO settings VALUES('12','logo','logo.png','2025-01-30 07:00:49','2025-03-23 04:30:21');
INSERT INTO settings VALUES('13','favicon','file_1742704225.png','2025-01-30 07:00:53','2025-03-23 04:30:25');
INSERT INTO settings VALUES('14','default_branch_name','Main Branch','2025-01-30 07:03:58','2025-01-30 14:32:16');
INSERT INTO settings VALUES('15','address','','2025-01-30 07:03:58','2025-01-30 14:32:16');
INSERT INTO settings VALUES('16','currency_position','left','2025-01-30 07:04:39','2025-01-30 07:04:39');
INSERT INTO settings VALUES('17','date_format','d/M/Y','2025-01-30 07:04:39','2025-01-30 07:04:39');
INSERT INTO settings VALUES('18','time_format','12','2025-01-30 07:04:39','2025-01-30 07:04:39');
INSERT INTO settings VALUES('19','member_signup','0','2025-01-30 07:04:39','2025-01-30 07:04:39');
INSERT INTO settings VALUES('20','email_2fa_status','0','2025-01-30 07:04:39','2025-01-30 07:04:39');



DROP TABLE IF EXISTS transaction_categories;

CREATE TABLE `transaction_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `related_to` varchar(2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS transactions;

CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `trans_date` datetime NOT NULL,
  `savings_account_id` bigint(20) unsigned DEFAULT NULL,
  `charge` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `gateway_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `dr_cr` varchar(2) NOT NULL,
  `type` varchar(30) NOT NULL,
  `method` varchar(20) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `note` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `loan_id` bigint(20) DEFAULT NULL,
  `ref_id` bigint(20) DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Parent transaction id',
  `gateway_id` bigint(20) DEFAULT NULL COMMENT 'PayPal | Stripe | Other Gateway',
  `created_user_id` bigint(20) DEFAULT NULL,
  `collector_id` bigint(20) unsigned DEFAULT NULL,
  `updated_user_id` bigint(20) DEFAULT NULL,
  `branch_id` bigint(20) DEFAULT NULL,
  `transaction_details` text DEFAULT NULL,
  `tracking_id` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_member_id_foreign` (`member_id`),
  KEY `transactions_savings_account_id_foreign` (`savings_account_id`),
  KEY `transactions_parent_id_foreign` (`parent_id`),
  KEY `transactions_collector_id_foreign` (`collector_id`),
  CONSTRAINT `transactions_collector_id_foreign` FOREIGN KEY (`collector_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_savings_account_id_foreign` FOREIGN KEY (`savings_account_id`) REFERENCES `savings_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO transactions VALUES('1','1','2025-01-30 10:30:58','1','','100.00','0.00','cr','Deposit','Manual','2','','Initial Deposit','','','','','1','6','','','','','2025-01-30 10:30:58','2025-01-30 10:30:58');
INSERT INTO transactions VALUES('2','1','2025-01-30 11:18:00','1','','500.00','0.00','cr','Deposit','Manual','2','','Daily Savings','','','','','3','6','','','','','2025-01-30 11:19:06','2025-01-30 11:19:06');
INSERT INTO transactions VALUES('3','1','2025-01-30 11:20:00','1','','200.00','0.00','dr','Withdraw','Manual','2','','Withdrawal','','','','','3','','','','','','2025-01-30 11:21:11','2025-01-30 11:21:11');
INSERT INTO transactions VALUES('4','2','2025-02-02 08:28:31','2','','100.00','0.00','cr','Deposit','Manual','2','','Initial Deposit','','','','','1','6','','','','','2025-02-02 08:28:31','2025-02-02 08:28:31');
INSERT INTO transactions VALUES('6','1','2025-02-05 22:04:55','1','10.00','90.00','0.00','dr','Withdraw','Manual','2','','Withdraw Money via Bank Transfer','','','','','5','','','','','','2025-02-05 22:04:55','2025-02-05 23:52:29');
INSERT INTO transactions VALUES('7','1','2025-02-05 22:04:55','1','','10.00','0.00','dr','Fee','Manual','2','','Bank Transfer Withdraw Fee','','','6','','5','','','','','','2025-02-05 22:04:55','2025-02-05 23:52:29');
INSERT INTO transactions VALUES('8','1','2025-02-06 00:11:00','1','20.00','180.00','0.00','dr','Withdraw','Manual','2','','Withdraw Money via Bank Transfer','','','','','1','','1','','','','2025-02-06 00:11:13','2025-02-06 00:13:13');
INSERT INTO transactions VALUES('9','1','2025-02-06 00:11:13','1','','20.00','0.00','dr','Fee','Manual','2','','Bank Transfer Withdraw Fee','','','8','','1','','','','','','2025-02-06 00:11:13','2025-02-06 00:11:13');
INSERT INTO transactions VALUES('10','1','2025-02-06 00:46:08','1','5.00','45.00','0.00','dr','Withdraw','Manual','2','','Withdraw Money via Bank Transfer','','','','','3','','','','','','2025-02-06 00:46:08','2025-02-06 00:46:08');
INSERT INTO transactions VALUES('11','1','2025-02-06 00:46:00','1','','5.00','0.00','dr','Fee','Manual','2','','Bank Transfer Withdraw Fee','','','10','','3','','1','','','','2025-02-06 00:46:08','2025-02-06 00:50:12');
INSERT INTO transactions VALUES('16','2','2025-02-07 09:08:00','2','','400.00','0.00','cr','Deposit','Manual','2','','group savings','','','','','1','6','','','','','2025-02-07 09:09:23','2025-02-07 09:09:23');
INSERT INTO transactions VALUES('17','1','2025-02-07 21:11:00','1','','100.00','0.00','cr','Deposit','Manual','2','','Initial Deposit','','','','','1','6','','','','','2025-02-07 21:12:27','2025-02-07 21:12:27');
INSERT INTO transactions VALUES('20','2','2025-02-08 08:31:00','2','150.00','850.00','0.00','dr','Withdraw','Manual','2','','Withdraw Money via Group Withdrawal','','','','','1','','','','','','2025-02-08 08:31:40','2025-02-08 08:31:40');
INSERT INTO transactions VALUES('21','2','2025-02-08 08:31:40','2','','150.00','0.00','dr','Fee','Manual','2','','Group Withdrawal Withdraw Fee','','','20','','1','','','','','','2025-02-08 08:31:40','2025-02-08 08:31:40');
INSERT INTO transactions VALUES('22','1','2025-02-14 07:29:00','1','','2050.00','0.00','cr','Deposit','Manual','2','','Daily Deposit','','','','','1','6','','','','','2025-02-14 07:47:28','2025-02-14 07:47:28');
INSERT INTO transactions VALUES('23','3','2025-02-14 08:17:40','4','','200.00','0.00','cr','Deposit','Manual','2','','Initial Deposit','','','','','1','','','','','','2025-02-14 08:17:40','2025-02-14 08:17:40');
INSERT INTO transactions VALUES('25','1','2025-02-14 08:20:00','1','20.00','180.00','0.00','dr','Withdraw','Manual','2','','Withdraw Money via Individual Withdrawal','','','','','1','','','','','','2025-02-14 08:21:45','2025-02-14 08:21:45');
INSERT INTO transactions VALUES('26','1','2025-02-14 08:21:45','1','','20.00','0.00','dr','Fee','Manual','2','','Individual Withdrawal Withdraw Fee','','','25','','1','','','','','','2025-02-14 08:21:45','2025-02-14 08:21:45');
INSERT INTO transactions VALUES('27','3','2025-02-14 08:22:00','4','30.00','170.00','0.00','dr','Withdraw','Manual','2','','Withdraw Money via Group Withdrawal','','','','','1','','','','','','2025-02-14 08:23:23','2025-02-14 08:23:23');
INSERT INTO transactions VALUES('28','3','2025-02-14 08:23:23','4','','30.00','0.00','dr','Fee','Manual','2','','Group Withdrawal Withdraw Fee','','','27','','1','','','','','','2025-02-14 08:23:23','2025-02-14 08:23:23');
INSERT INTO transactions VALUES('29','1','2025-02-14 08:34:00','1','2.00','98.00','0.00','dr','Withdraw','Manual','2','','Withdraw Money via Individual Withdrawal','','','','','1','','','','','','2025-02-14 08:34:57','2025-02-14 08:34:57');
INSERT INTO transactions VALUES('30','1','2025-02-14 08:34:57','1','','2.00','0.00','dr','Fee','Manual','2','','Individual Withdrawal Withdraw Fee','','','29','','1','','','','','','2025-02-14 08:34:57','2025-02-14 08:34:57');
INSERT INTO transactions VALUES('34','1','2025-03-15 13:23:00','1','1.20','58.80','0.00','dr','Withdraw','Manual','2','','Withdraw Money via Individual Withdrawal','','','','','1','','','','','','2025-03-15 13:26:40','2025-03-15 13:26:40');
INSERT INTO transactions VALUES('35','1','2025-03-15 13:26:40','1','','1.20','0.00','dr','Fee','Manual','2','','Individual Withdrawal Withdraw Fee','','','34','','1','','','','','','2025-03-15 13:26:40','2025-03-15 13:26:40');
INSERT INTO transactions VALUES('41','1','2025-03-22 04:36:00','1','','200.00','0.00','cr','Deposit','Manual','2','','Friday Deposit','','','','','1','6','','','','','2025-03-22 04:37:25','2025-03-22 04:37:25');
INSERT INTO transactions VALUES('42','1','2025-03-22 12:53:00','1','','100.00','0.00','cr','Deposit','Manual','2','','Text Depo','','','','','1','6','','','','','2025-03-22 12:57:06','2025-03-22 12:57:06');
INSERT INTO transactions VALUES('46','1','2025-03-22 13:30:00','1','20.00','980.00','0.00','dr','Withdraw','Manual','2','','Withdraw Money via Individual Withdrawal','','','','','1','','','','','','2025-03-22 13:31:05','2025-03-22 13:31:05');
INSERT INTO transactions VALUES('47','1','2025-03-22 13:31:05','1','','20.00','0.00','dr','Fee','Manual','2','','Individual Withdrawal Withdraw Fee','','','46','','1','','','','','','2025-03-22 13:31:05','2025-03-22 13:31:05');
INSERT INTO transactions VALUES('50','2','2025-03-22 14:15:10','2','','100.00','0.00','cr','Deposit','Manual','2','Contribution for Group Savings','Group Susu Contribution','','','','','1','6','','','','','2025-03-22 14:15:10','2025-03-22 14:15:10');
INSERT INTO transactions VALUES('51','2','2025-03-22 16:17:45','2','','100.00','0.00','cr','Deposit','Manual','2','Contribution for Group Savings','Group Susu Contribution','','','','','1','6','','','','','2025-03-22 16:17:45','2025-03-22 16:17:45');
INSERT INTO transactions VALUES('53','3','2025-03-22 18:04:46','4','','200.00','0.00','cr','Deposit','Manual','2','Contribution for Group Savings','Group Susu Contribution','','','','','1','6','','','','','2025-03-22 18:04:46','2025-03-22 18:04:46');
INSERT INTO transactions VALUES('54','2','2025-03-23 22:02:05','10','','100.00','0.00','cr','Deposit','Manual','2','','Initial Deposit','','','','','1','6','','','','','2025-03-23 22:02:05','2025-03-23 22:02:05');
INSERT INTO transactions VALUES('55','1','2025-03-23 22:05:18','3','','100.00','0.00','cr','Deposit','Manual','2','Contribution for Group Savings','Group Susu Contribution','','','','','1','6','','','','','2025-03-23 22:05:18','2025-03-23 22:05:18');
INSERT INTO transactions VALUES('56','2','2025-03-23 22:05:45','10','','200.00','0.00','cr','Deposit','Manual','2','Contribution for Group Savings','Group Susu Contribution','','','','','1','6','','','','','2025-03-23 22:05:45','2025-03-23 22:05:45');
INSERT INTO transactions VALUES('57','1','2025-03-24 04:22:47','11','','200.00','0.00','cr','Deposit','Manual','2','','Initial Deposit','','','','','1','6','','','','','2025-03-24 04:22:47','2025-03-24 04:22:47');
INSERT INTO transactions VALUES('58','1','2025-03-24 04:27:01','3','','100.00','0.00','cr','Deposit','Manual','2','Contribution for Group Savings','Group Susu Contribution','','','','','1','6','','','','','2025-03-24 04:27:01','2025-03-24 04:27:01');



DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `role_id` bigint(20) DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `status` int(11) NOT NULL,
  `profile_picture` varchar(191) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) DEFAULT NULL,
  `two_factor_code` varchar(10) DEFAULT NULL,
  `two_factor_expires_at` datetime DEFAULT NULL,
  `two_factor_code_count` int(11) NOT NULL DEFAULT 0,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `otp_count` int(11) NOT NULL DEFAULT 0,
  `provider` varchar(191) DEFAULT NULL,
  `provider_id` varchar(191) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_branch_id_foreign` (`branch_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users VALUES('1','John Admin','admin@savings.com','admin','','','1','profile_1738220922.png','2025-01-30 06:10:16','$2y$10$EsPvzl2sOnb8ikZP7bCCr.gkFLNl5RF7yrwgtExS5edRp1K.CAdpW','','','0','','','0','','','','2025-01-30 06:10:16','2025-01-30 07:08:42');
INSERT INTO users VALUES('2','Raf Ick','mustapharafick3@gmail.com','user','2','','1','default.png','2025-01-30 10:17:28','$2y$10$UTCdKrd8aalGw1gmTUF4RexmjHjB5iH7T2HAOj4soMI4dRPPObfAy','','','0','','','0','','','','2025-01-30 10:17:28','2025-01-30 10:17:28');
INSERT INTO users VALUES('3','Diana Counter One','counter@savings.com','user','3','','1','default.png','2025-01-30 10:38:23','$2y$10$dc0orNl0.wPeaqdf2oRFKeWKzac7ZYluKvLza5/vZxtXN3O.mOq8C','','','0','','','0','','','','2025-01-30 10:38:23','2025-01-30 10:38:23');
INSERT INTO users VALUES('5','Rafick Mustapha','rafick@savings.com','customer','','','1','','','$2y$10$Dv31btwGMYmsbh0bVMrg4eQEqLIhJYlv9HMF7T6heGZKDZ.6dKUOS','','','0','','','0','','','22nIQpL0b3nICw92gfFtakEOYeZVdZXlFk0yY2LDYOXsGwNljJkbEPPFeOOU','2025-02-03 17:23:41','2025-02-03 17:23:41');
INSERT INTO users VALUES('6','Jane Collector One','jane@savings.com','user','5','','1','default.png','2025-02-14 07:18:33','$2y$10$au8YiyCI0YrNCxOsuehh.eOumYGYw6HwupBQk.Dpy1PzIlakAHVPq','','','0','','','0','','','','2025-02-14 07:18:33','2025-02-14 07:18:33');



DROP TABLE IF EXISTS withdraw_methods;

CREATE TABLE `withdraw_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `currency_id` bigint(20) NOT NULL,
  `minimum_amount` decimal(10,2) NOT NULL,
  `maximum_amount` decimal(10,2) NOT NULL,
  `fixed_charge` decimal(10,2) NOT NULL,
  `charge_in_percentage` decimal(10,2) NOT NULL,
  `descriptions` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `requirements` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO withdraw_methods VALUES('1','Individual Withdrawal','1738867248download.jpeg','1','0.00','0.00','0.00','0.00','','1','null','2025-02-03 17:17:53','2025-02-06 18:40:48');
INSERT INTO withdraw_methods VALUES('2','Group Withdrawal','1738867016download (1).jpeg','1','0.00','0.00','0.00','0.00','','1','null','2025-02-06 18:36:56','2025-02-06 18:36:56');



DROP TABLE IF EXISTS withdraw_requests;

CREATE TABLE `withdraw_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `method_id` bigint(20) unsigned NOT NULL,
  `debit_account_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `converted_amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `attachment` varchar(191) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `transaction_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdraw_requests_member_id_foreign` (`member_id`),
  KEY `withdraw_requests_method_id_foreign` (`method_id`),
  KEY `withdraw_requests_debit_account_id_foreign` (`debit_account_id`),
  CONSTRAINT `withdraw_requests_debit_account_id_foreign` FOREIGN KEY (`debit_account_id`) REFERENCES `savings_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `withdraw_requests_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `withdraw_requests_method_id_foreign` FOREIGN KEY (`method_id`) REFERENCES `withdraw_methods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO withdraw_requests VALUES('1','1','1','1','100.00','100.00','fhddbfsfh','{\"Wallet_Number\":\"120000\",\"Email\":\"mustapharafick3@gmail.com\"}','','2','6','2025-02-05 22:04:55','2025-02-05 23:52:29');



