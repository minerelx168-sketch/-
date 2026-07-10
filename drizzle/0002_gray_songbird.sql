CREATE TABLE `credit_transactions` (
	`id` int AUTO_INCREMENT NOT NULL,
	`userId` int NOT NULL,
	`amount` decimal(10,2) NOT NULL,
	`type` enum('TOPUP','USAGE','REFUND','ADJUSTMENT','BONUS') NOT NULL,
	`referenceType` varchar(64),
	`referenceId` varchar(128),
	`balanceAfter` decimal(10,2) NOT NULL,
	`description` text,
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `credit_transactions_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
CREATE TABLE `topup_orders` (
	`id` int AUTO_INCREMENT NOT NULL,
	`publicId` varchar(64) NOT NULL,
	`userId` int NOT NULL,
	`amount` decimal(10,2) NOT NULL,
	`chargeAmount` decimal(10,2) NOT NULL,
	`currency` varchar(8) NOT NULL DEFAULT 'USD',
	`status` enum('PENDING','PAID','CREDITED','FAILED','EXPIRED','REFUNDED') NOT NULL DEFAULT 'PENDING',
	`provider` varchar(32) NOT NULL DEFAULT 'lemonsqueezy',
	`providerChargeId` varchar(255),
	`idempotencyKey` varchar(128) NOT NULL,
	`feePct` decimal(5,2) NOT NULL DEFAULT '0.00',
	`paidAt` timestamp,
	`creditedAt` timestamp,
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	`updatedAt` timestamp NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP,
	CONSTRAINT `topup_orders_id` PRIMARY KEY(`id`),
	CONSTRAINT `topup_orders_publicId_unique` UNIQUE(`publicId`),
	CONSTRAINT `topup_orders_idempotencyKey_unique` UNIQUE(`idempotencyKey`)
);
--> statement-breakpoint
CREATE TABLE `webhook_events` (
	`id` int AUTO_INCREMENT NOT NULL,
	`provider` varchar(32) NOT NULL,
	`eventId` varchar(128) NOT NULL,
	`eventType` varchar(128),
	`signatureOk` boolean NOT NULL DEFAULT true,
	`processed` boolean NOT NULL DEFAULT false,
	`processedAt` timestamp,
	`processingError` text,
	`rawBody` text,
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `webhook_events_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
DROP TABLE `credits`;--> statement-breakpoint
DROP TABLE `transactions`;--> statement-breakpoint
ALTER TABLE `users` ADD `cachedBalance` decimal(10,2) DEFAULT '0.00' NOT NULL;