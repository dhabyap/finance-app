-- Migration: Add role and goal_amount columns to users table
-- Date: 2026-04-29
-- Issue: #42 - Sync database schema with code for user role and goal_amount

ALTER TABLE `users`
  ADD COLUMN `role` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user' AFTER `password`,
  ADD COLUMN `goal_amount` decimal(15,2) NOT NULL DEFAULT 0 AFTER `role`;
