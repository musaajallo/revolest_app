# Super Admin Dashboard Design

## Overview

A comprehensive dashboard for the super_admin role in the SÀ Property Management System. Inspired by modern analytics dashboards with clean layouts, visual charts, and actionable insights.

---

## Dashboard Layout

### Row 1: Key Performance Stats (4 Cards)

| Widget | Description | Visual |
|--------|-------------|--------|
| **Total Properties** | Count of all properties | Number with trend (↑/↓ % from last month) |
| **Total Tenants** | Active tenants in system | Number with trend indicator |
| **Total Owners** | Property owners registered | Number with trend indicator |
| **Total Agents** | Active agents | Number with trend indicator |

### Row 2: Financial Overview (3 Cards)

| Widget | Description | Visual |
|--------|-------------|--------|
| **Total Revenue (GMD)** | Sum of all payments collected | Large number with currency |
| **Pending Payments** | Outstanding payment amounts | Number with warning color if high |
| **This Month's Revenue** | Current month collections | Number with comparison to last month |

### Row 3: Growth Charts (2 Columns)

| Widget | Description | Chart Type |
|--------|-------------|------------|
| **Platform Growth** | Monthly registrations (Tenants, Owners, Agents) over 12 months | Line/Area Chart |
| **Property Distribution** | Properties by type (House, Apartment, Land, Commercial) | Doughnut/Pie Chart |

### Row 4: Lease & Revenue Analytics (2 Columns)

| Widget | Description | Chart Type |
|--------|-------------|------------|
| **Monthly Revenue Trend** | Revenue collected over past 12 months | Bar Chart |
| **Lease Status Overview** | Active, Expired, Pending lease breakdown | Doughnut Chart with center stat |

### Row 5: Circular Stats (3 Cards) - Inspired by Reference

| Widget | Description | Visual |
|--------|-------------|--------|
| **Inquiry Requests** | Total inquiries with New vs Returning breakdown | Circular progress with icon |
| **Active Listings** | Properties currently listed | Circular progress with count |
| **Occupancy Rate** | Percentage of properties occupied | Circular percentage gauge |

### Row 6: Recent Activity & Tables

| Widget | Description |
|--------|-------------|
| **Recent Inquiries** | Latest 5 inquiries with name, email, property, status |
| **Upcoming Lease Expirations** | Leases expiring in next 30 days |
| **Recent Payments** | Latest 5 payment transactions |

### Row 7: Quick Actions & Alerts

| Widget | Description |
|--------|-------------|
| **Open Repair Requests** | Count of unresolved repair requests |
| **Open Complaints** | Count of unresolved complaints |
| **System Alerts** | Important notifications (e.g., expiring leases, overdue payments) |

---

## Color Scheme

- **Primary (Red):** #d41313 - Main brand color
- **Success (Green):** #00a676 - Positive trends, active status
- **Warning (Amber):** #f59e0b - Pending items, warnings
- **Danger (Red):** #ef4444 - Negative trends, expired, overdue
- **Info (Blue):** #3b82f6 - Neutral information

---

## Widget Components to Create

1. `StatsOverviewWidget` - Key metrics with trends
2. `PlatformGrowthChart` - Line chart for user registrations
3. `PropertyDistributionChart` - Doughnut chart for property types
4. `RevenueChart` - Bar chart for monthly revenue
5. `LeaseStatusChart` - Doughnut chart for lease statuses
6. `CircularStatsWidget` - Circular progress cards (Inquiries, Listings, Occupancy)
7. `RecentInquiriesTable` - Table widget for inquiries
8. `UpcomingExpirations` - Table for expiring leases
9. `RecentPaymentsTable` - Table for recent payments
10. `AlertsWidget` - System notifications and alerts

---

## Technical Implementation

### Filament Widgets Used:
- `Filament\Widgets\StatsOverviewWidget` - For stat cards
- `Filament\Widgets\ChartWidget` - For charts (Line, Bar, Doughnut, Pie)
- `Filament\Widgets\Widget` - For custom widgets

### Chart Library:
- Chart.js (built into Filament)

### Data Sources:
- `App\Models\Property`
- `App\Models\Tenant`
- `App\Models\Owner`
- `App\Models\Agent`
- `App\Models\Lease`
- `App\Models\Payment`
- `App\Models\Inquiry`
- `App\Models\RepairRequest`
- `App\Models\Complaint`

---

## Responsive Design

- **Desktop:** Full 3-4 column layout
- **Tablet:** 2 column layout
- **Mobile:** Single column, stacked widgets

---

## Future Enhancements

1. Date range filters for charts
2. Export dashboard data to PDF
3. Customizable widget arrangement
4. Real-time updates with Livewire polling
5. Comparison mode (this month vs last month)
6. Goal tracking (e.g., target revenue, target listings)
