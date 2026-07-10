CREATE TABLE `credits` (
	`id` int AUTO_INCREMENT NOT NULL,
	`userId` int NOT NULL,
	`balance` int NOT NULL DEFAULT 0,
	`updatedAt` timestamp NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP,
	CONSTRAINT `credits_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
CREATE TABLE `transactions` (
	`id` int AUTO_INCREMENT NOT NULL,
	`userId` int NOT NULL,
	`amount` int NOT NULL,
	`type` enum('topup','usage','refund') NOT NULL,
	`status` enum('pending','completed','failed') NOT NULL DEFAULT 'completed',
	`description` text,
	`lemonOrderId` varchar(128),
	`lemonProductId` varchar(128),
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `transactions_id` PRIMARY KEY(`id`)
);
