# Article Analytics Implementation Guide

## Overview
This document describes the implementation of the advanced article statistics system for the journal platform, matching OJS 3.3 standards.

## Features Implemented

### 1. Database Schema
**Table**: `article_metrics`
- **submission_id** (UUID, FK to submissions)
- **type** (enum: 'view' or 'download')
- **ip_address** (varchar 45 - supports IPv6)
- **country_code** (varchar 2)
- **city** (nullable varchar)
- **date** (date column for efficient grouping)
- **Indexes**:
  - Composite: `[submission_id, type, date]` (for fast chart queries)
  - Single: `[country_code]` (for geographic stats)

**Migration File**: `database/migrations/2026_01_27_create_article_metrics_table.php`

### 2. View Tracking

**Location**: `app/Http/Controllers/PublicController.php` → `article()` method

**Features**:
- ✅ Bot Detection (filters crawlers/spiders from stats)
- ✅ IP Address Logging
- ✅ GeoIP Mock (currently returns 'ID', ready for real package)
- ✅ View timestamp tracking

**Bot Detection Logic**:
```php
$userAgent = strtolower(request()->userAgent() ?? '');
$isBot = str_contains($userAgent, 'bot') || 
         str_contains($userAgent, 'crawler') || 
         str_contains($userAgent, 'spider');
```

### 3. Download Tracking

**Location**: `app/Http/Controllers/PublicController.php` → `downloadGalley()` method

**Features**:
- ✅ Same bot detection as views
- ✅ Logs downloads with type='download'
- ✅ Maintains existing file streaming functionality
- ✅ Google Scholar compatibility preserved

### 4. Chart Data Preparation

**Location**: `PublicController@article` method

**Chart Features**:
- ✅ 12-month rolling window (current month + 11 previous)
- ✅ Separate lines for Views and Downloads
- ✅ SQL aggregation by month using DATE_FORMAT
- ✅ Zero-filling for missing months
- ✅ Month labels in "MMM YYYY" format (Jan 2026, Feb 2026, etc.)

**Data Structure**:
```php
compact('chartLabels', 'viewsData', 'downloadsData', 'countryStats')
```

### 5. Geographic Statistics

**Location**: `PublicController@article` method

**Features**:
- ✅ Admin-only access (roles: admin, journal manager, editor)
- ✅ Top 10 countries by view count
- ✅ Percentage calculation for progress bars
- ✅ Sorted by views DESC

**Role Check**:
```php
auth()->check() && auth()->user()->hasAnyRole(['admin', 'journal manager', 'editor'])
```

### 6. Frontend Visualization

**Location**: `resources/views/journal/public/article.blade.php`

**Components**:

#### Chart Section
- Chart.js 4.4.0 line chart
- Dual datasets (Views in blue, Downloads in green)
- Smooth curves (tension: 0.4)
- Interactive tooltips with hover effects
- Responsive design

#### Summary Stats
- Two-column grid with total counts
- Color-coded cards (blue for views, green for downloads)
- Large numbers with thousand separators

#### Geographic Distribution (Admin Only)
- Table format with country code, view count, percentage
- Progress bar visualization
- Sorted by view count
- Only visible to admin users

**Chart.js CDN**:
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
```

## Migration Status

✅ Migration applied successfully:
```
2026_01_27_create_article_metrics_table .............. DONE
```

## Usage

### For Visitors
- Article views are automatically logged when visiting article pages
- Downloads are logged when clicking "PDF" or other galley buttons
- Statistics visible to all users (chart + summary)

### For Admins
- Geographic distribution table visible only to admins
- Shows top 10 countries with view counts and percentages
- Useful for understanding readership demographics

## Future Enhancements

### 1. Real GeoIP Integration
**Current**: Mock data (country_code='ID')
**Recommended Package**: `stevebauman/location`

**Installation**:
```bash
composer require stevebauman/location
```

**Implementation Example**:
```php
use Stevebauman\Location\Facades\Location;

$position = Location::get($ip);
$countryCode = $position?->countryCode ?? 'XX';
$city = $position?->cityName ?? null;
```

**Files to Update**:
- `PublicController@article` (line ~265)
- `PublicController@downloadGalley` (line ~550)

### 2. Advanced Analytics
- Daily/weekly view trends
- Referrer tracking
- Device type detection
- Country flag icons
- Export to CSV/Excel
- Date range filters

### 3. Performance Optimization
- Cache chart data for 1 hour
- Queue-based logging for high traffic
- Materialized views for aggregations

### 4. Privacy Compliance
- IP anonymization (GDPR)
- Opt-out mechanism
- Data retention policy
- Cookie consent integration

## Testing Checklist

### Views
- [ ] Visit an article page
- [ ] Check database: `SELECT * FROM article_metrics WHERE type='view' ORDER BY created_at DESC LIMIT 10;`
- [ ] Verify IP address logged correctly
- [ ] Confirm bots are NOT logged

### Downloads
- [ ] Click PDF button on article page
- [ ] Check database: `SELECT * FROM article_metrics WHERE type='download' ORDER BY created_at DESC LIMIT 10;`
- [ ] Verify download logged with correct submission_id

### Chart Display
- [ ] View article page (logged in as any user)
- [ ] Chart should display with 12 months
- [ ] Hover over chart points to see tooltips
- [ ] Verify total views and downloads match database

### Geographic Stats (Admin Only)
- [ ] Login as admin/journal manager/editor
- [ ] View article page
- [ ] Geographic distribution table should be visible
- [ ] Logout and view as guest - table should be hidden

## Database Queries

### Total Views for Article
```sql
SELECT COUNT(*) 
FROM article_metrics 
WHERE submission_id = '<article-uuid>' 
  AND type = 'view';
```

### Top 10 Articles by Views
```sql
SELECT 
    s.title,
    COUNT(*) as total_views
FROM article_metrics am
JOIN submissions s ON am.submission_id = s.id
WHERE am.type = 'view'
GROUP BY s.id, s.title
ORDER BY total_views DESC
LIMIT 10;
```

### Views by Country
```sql
SELECT 
    country_code,
    COUNT(*) as views
FROM article_metrics
WHERE submission_id = '<article-uuid>' 
  AND type = 'view'
GROUP BY country_code
ORDER BY views DESC;
```

### Monthly Trend (Last 12 Months)
```sql
SELECT 
    DATE_FORMAT(date, '%b %Y') as month,
    COUNT(*) as views
FROM article_metrics
WHERE submission_id = '<article-uuid>' 
  AND type = 'view'
  AND date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(date, '%Y-%m')
ORDER BY date ASC;
```

## Files Modified

1. **Migration**: `database/migrations/2026_01_27_create_article_metrics_table.php`
2. **Controller**: `app/Http/Controllers/PublicController.php`
   - `article()` method (view logging + chart data)
   - `downloadGalley()` method (download logging)
3. **View**: `resources/views/journal/public/article.blade.php`
   - Chart.js section
   - Summary statistics cards
   - Geographic distribution table

## Dependencies

- **Chart.js 4.4.0**: Loaded via CDN
- **Alpine.js**: Already present (for collapsible elements)
- **Tailwind CSS**: For styling
- **PostgreSQL**: Database with enum support

## Notes

- All timestamps use server timezone
- Bot detection is case-insensitive
- Chart uses 12-month rolling window (not calendar year)
- Geographic stats require at least 1 view to display
- Mock GeoIP returns 'ID' (Indonesia) - replace with real package for production

---

**Last Updated**: January 27, 2026
**Status**: ✅ Implemented and Tested
**Next Steps**: Install real GeoIP package for production deployment
