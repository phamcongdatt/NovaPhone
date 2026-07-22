# Queue & Mail Setup Guide

## 🚀 Queue Configuration

### Current Setup
```env
QUEUE_CONNECTION=database
```

### Required Database Tables
```bash
php artisan migrate
# Creates: jobs, failed_jobs tables
```

---

## 📧 Mail Configuration

### Current Setup (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=phamdat12213443@gmail.com
MAIL_PASSWORD=rmdssbuiojgvljdr
MAIL_FROM_ADDRESS="phamdat12213443@gmail.com"
MAIL_FROM_NAME="NovaPhone"
```

### Verify Gmail Configuration
1. Enable SMTP in Gmail: https://myaccount.google.com/security
2. Use App Password instead of regular password (if 2FA enabled)
3. Current: Using App Password ✅

---

## 🔄 Queue Worker Setup

### Development
```bash
# Start queue worker (processes jobs one at a time)
php artisan queue:work

# With delay between attempts
php artisan queue:work --delay=3

# With timeout
php artisan queue:work --timeout=60

# Monitor failed jobs
php artisan queue:failed
```

### Production (using Supervisor)

#### 1. Install Supervisor
```bash
# Ubuntu/Debian
sudo apt-get install supervisor

# Or using package manager
```

#### 2. Create Supervisor Config
**File:** `/etc/supervisor/conf.d/laravel-worker.conf`

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/NovaPhone/artisan queue:work database --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/NovaPhone/storage/logs/worker.log
stopwaitsecs=3600
```

#### 3. Reload Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

#### 4. Monitor
```bash
sudo supervisorctl status
sudo tail -f /var/log/supervisor/laravel-worker.log
```

---

## ✅ Testing Mail & Queue

### 1. Test Mail Config
```bash
php artisan tinker

Mail::raw('Test message', function ($message) {
    $message->to('test@example.com');
});
```

### 2. Test Queue with Order Cancellation
```bash
# Create test order
php artisan tinker
$order = Order::find(1);

# Cancel via command (with admin)
php artisan order:test-cancel 1 --admin-user=2

# Check if job queued
SELECT COUNT(*) FROM jobs;

# Process queue
php artisan queue:work
```

### 3. Verify Email Sent
- Check user's email inbox
- Or check database: `SELECT * FROM notifications;`

### 4. Debug Failed Jobs
```bash
# View failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {id}

# Clear failed jobs
php artisan queue:flush
```

---

## 🔍 Monitoring

### 1. Check Queue Status
```sql
-- Pending jobs
SELECT id, queue, attempts, created_at FROM jobs WHERE deleted_at IS NULL;

-- Failed jobs
SELECT id, queue, exception, created_at FROM failed_jobs ORDER BY created_at DESC;

-- Processed notifications
SELECT id, notifiable_id, type, created_at FROM notifications ORDER BY created_at DESC LIMIT 10;
```

### 2. Monitor in Real-Time
```bash
# Terminal 1: Queue worker
php artisan queue:work

# Terminal 2: Monitor
watch -n 1 "mysql -u root -e 'SELECT COUNT(*) as pending_jobs FROM jobs;'"
```

### 3. Check Logs
```bash
tail -f storage/logs/laravel.log

# Filter for cancellation
grep -i "cancel\|OrderCancelled" storage/logs/laravel.log
```

---

## 🛠️ Troubleshooting

### Issue: Jobs Not Processing

**Symptom:** Jobs stay in `jobs` table, not processing

**Solutions:**
1. Check queue worker running: `php artisan queue:work`
2. Check job fails immediately → `php artisan queue:failed`
3. Check logs: `storage/logs/laravel.log`
4. Check permission: User running artisan has DB access
5. Check `jobs` table not full: `DELETE FROM jobs WHERE deleted_at IS NOT NULL;`

### Issue: Email Not Sending

**Symptom:** Job success but no email received

**Solutions:**
1. Check mail config in `.env`
2. Test mail: `php artisan tinker` → `Mail::raw('test', ...)`
3. Check Gmail app password (if 2FA)
4. Check firewall port 587 open
5. Check logs for SMTP error

### Issue: Notification Not Saving

**Symptom:** Email sent but notification not in DB

**Solutions:**
1. Check `notifications` table exists
2. Check `via()` returns 'database'
3. Check User has `Notifiable` trait
4. Clear notification cache: `php artisan cache:clear`

### Issue: Multiple Job Attempts

**Symptom:** Same notification sent multiple times

**Solutions:**
1. Set `--tries=1` in supervisor config
2. Set `--timeout=60` to avoid timeout retry
3. Check job not delayed forever
4. Monitor failed_jobs table

---

## 📊 Performance Tips

### 1. Queue Worker Optimization
```bash
# Process jobs faster
php artisan queue:work --sleep=0  # No delay between jobs

# But use caution:
php artisan queue:work --sleep=3 --max-jobs=1000 --max-time=3600
```

### 2. Database Cleanup
```sql
-- Delete old processed jobs (monthly)
DELETE FROM jobs WHERE deleted_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Delete old failed jobs (quarterly)  
DELETE FROM failed_jobs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### 3. Notification Cleanup
```sql
-- Delete old read notifications (yearly)
DELETE FROM notifications WHERE read_at IS NOT NULL AND created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

---

## 🔐 Security Notes

### Mail Credentials
- ✅ Using App Password (not regular password)
- ✅ Credentials in `.env` (not in code)
- ⚠️ Protect `.env` file access
- ⚠️ Rotate credentials periodically

### Queue Jobs
- ✅ No sensitive data in job payload
- ✅ Notification IDs only, lazy-load user
- ⚠️ Monitor for queue injection attacks
- ⚠️ Validate job data before processing

---

## 📝 Command Reference

### Queue Commands
```bash
# Process all jobs
php artisan queue:work

# Process specific queue
php artisan queue:work database

# Show worker status
php artisan queue:monitor

# Retry failed job
php artisan queue:retry {id}

# Flush all failed jobs
php artisan queue:flush

# Restart all workers (graceful)
php artisan queue:restart
```

### Mail Commands
```bash
# Test mail configuration
php artisan mail:test your-email@example.com

# List mailables
php artisan mail:list

# Retry failed mail jobs
php artisan queue:retry
```

---

## 🎯 Setup Checklist

### Initial Setup
- [x] Queue driver: database
- [x] Mail driver: smtp
- [x] Gmail configured
- [x] `.env` updated

### Before Production
- [ ] Create supervisor config
- [ ] Start queue worker
- [ ] Test full flow: cancel → email → notification
- [ ] Monitor logs for errors
- [ ] Setup log rotation
- [ ] Setup database cleanup cron

### Ongoing Maintenance
- [ ] Monitor queue depth (`SELECT COUNT(*) FROM jobs;`)
- [ ] Monitor failed jobs
- [ ] Check email delivery
- [ ] Review logs weekly
- [ ] Run cleanup queries monthly

---

## 🚀 Quick Start

### Development
```bash
# 1. Ensure migrations run
php artisan migrate

# 2. Start queue worker
php artisan queue:work

# 3. Test cancellation
php artisan order:test-cancel 1 --admin-user=2

# 4. Check email in inbox or DB
SELECT * FROM notifications ORDER BY created_at DESC;
```

### Production
```bash
# 1. Setup supervisor config
sudo nano /etc/supervisor/conf.d/laravel-worker.conf

# 2. Reload supervisor
sudo supervisorctl reread && sudo supervisorctl update

# 3. Start workers
sudo supervisorctl start laravel-worker:*

# 4. Verify
sudo supervisorctl status

# 5. Monitor
tail -f /path/to/storage/logs/worker.log
```

---

## 📚 Resources

- Laravel Queue: https://laravel.com/docs/11.x/queues
- Laravel Mail: https://laravel.com/docs/11.x/mail
- Supervisor: http://supervisord.org/
- Gmail App Passwords: https://myaccount.google.com/apppasswords

---

**Queue & Mail setup ready! ✅**
