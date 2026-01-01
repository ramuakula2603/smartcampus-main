# Fee Group-wise Collection Report - Visual Guide

## Page Layout Overview

This document describes the visual layout and user interface of the Fee Group-wise Collection Report.

---

## 1. Page Header

```
┌─────────────────────────────────────────────────────────────────┐
│ 📊 Fee Group-wise Collection Report                             │
│                                                                  │
│ Home > Finance Reports > Fee Group-wise Collection              │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. Filter Section

```
┌─────────────────────────────────────────────────────────────────┐
│ 🔍 Filters                                                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Session *          Class               Section                 │
│  [2024-2025 ▼]     [Select... ▼]       [Select... ▼]          │
│                                                                  │
│  Fee Group          From Date           To Date                 │
│  [Select... ▼]     [📅 dd/mm/yyyy]     [📅 dd/mm/yyyy]        │
│                                                                  │
│  Date Grouping                          [🔍 Search]             │
│  [None ▼]                                                       │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Features**:
- Session dropdown (required field marked with *)
- Multi-select dropdowns for Class, Section, Fee Group
- Date pickers for date range
- Date grouping options
- Blue "Search" button with search icon

---

## 3. Summary Statistics Card

```
┌─────────────────────────────────────────────────────────────────┐
│ 📊 Summary Statistics                    [Purple Gradient BG]   │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Total Fee Groups: 12        Total Amount: Rs. 1,250,000.00    │
│                                                                  │
│  Amount Collected: Rs. 950,000.00    Balance: Rs. 300,000.00   │
│                                                                  │
│  Overall Collection Percentage: 76%                             │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Features**:
- Beautiful purple gradient background
- White text for high contrast
- Key metrics displayed prominently
- Currency symbols and proper formatting

---

## 4. Fee Group Grid (4x4 Layout)

```
┌─────────────────────────────────────────────────────────────────┐
│ 📊 Fee Group-wise Collection (Top 16)                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
│  │ Tuition  │  │ Transport│  │ Library  │  │ Sports   │       │
│  │ Fee      │  │ Fee      │  │ Fee      │  │ Fee      │       │
│  │          │  │          │  │          │  │          │       │
│  │ Total:   │  │ Total:   │  │ Total:   │  │ Total:   │       │
│  │ Rs.500K  │  │ Rs.200K  │  │ Rs.100K  │  │ Rs.80K   │       │
│  │          │  │          │  │          │  │          │       │
│  │ Collected│  │ Collected│  │ Collected│  │ Collected│       │
│  │ Rs.450K  │  │ Rs.180K  │  │ Rs.95K   │  │ Rs.70K   │       │
│  │          │  │          │  │          │  │          │       │
│  │ Balance: │  │ Balance: │  │ Balance: │  │ Balance: │       │
│  │ Rs.50K   │  │ Rs.20K   │  │ Rs.5K    │  │ Rs.10K   │       │
│  │          │  │          │  │          │  │          │       │
│  │ ████████░│  │ ████████░│  │ █████████│  │ ████████░│       │
│  │   90%    │  │   90%    │  │   95%    │  │   87.5%  │       │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
│                                                                  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
│  │ Lab Fee  │  │ Exam Fee │  │ Annual   │  │ Admission│       │
│  │          │  │          │  │ Fee      │  │ Fee      │       │
│  │ ...      │  │ ...      │  │ ...      │  │ ...      │       │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
│                                                                  │
│  [... 8 more cards in rows 3 and 4 ...]                        │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Features**:
- 16 cards in 4x4 grid
- Each card shows fee group name, amounts, and progress bar
- Progress bars color-coded:
  - 🟢 Green (80%+): Good collection
  - 🟡 Yellow (50-79%): Moderate collection
  - 🔴 Red (<50%): Low collection
- Hover effect: Cards lift up with shadow
- Responsive: Adjusts to 3x3, 2x2, or 1x1 on smaller screens

---

## 5. Charts Section

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                  │
│  ┌────────────────────────────┐  ┌────────────────────────────┐│
│  │ 🥧 Collection Distribution │  │ 📊 Fee Group Comparison    ││
│  ├────────────────────────────┤  ├────────────────────────────┤│
│  │                            │  │                            ││
│  │      ╱─────╲               │  │  ┃                         ││
│  │     ╱       ╲              │  │  ┃ ██                      ││
│  │    │  PIE    │             │  │  ┃ ██  ██                  ││
│  │    │ CHART   │             │  │  ┃ ██  ██  ██              ││
│  │     ╲       ╱              │  │  ┃ ██  ██  ██  ██          ││
│  │      ╲─────╱               │  │  ┗━━━━━━━━━━━━━━━━━━━━━━━━││
│  │                            │  │    T   T   L   S   L       ││
│  │  Legend:                   │  │    u   r   i   p   a       ││
│  │  ■ Tuition Fee             │  │    i   a   b   o   b       ││
│  │  ■ Transport Fee           │  │    t   n   r   r          ││
│  │  ■ Library Fee             │  │    i   s   a   t   F       ││
│  │  ■ Sports Fee              │  │    o   p   r   s   e       ││
│  │  ■ Lab Fee                 │  │    n   o   y              ││
│  │  ...                       │  │                            ││
│  │                            │  │  ■ Collected  ■ Balance    ││
│  └────────────────────────────┘  └────────────────────────────┘│
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Features**:
- **Left**: Pie chart showing collection distribution
- **Right**: Bar chart comparing collected vs balance
- Interactive tooltips on hover
- Responsive legends
- Color-coded segments/bars
- Shows top 10 fee groups

---

## 6. Detailed Data Table

```
┌─────────────────────────────────────────────────────────────────┐
│ 📋 Detailed Fee Collection Records                              │
│                                          [📊 Excel] [📄 CSV]    │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Show [25 ▼] entries          Search: [____________] 🔍         │
│                                                                  │
│  ┌──────┬─────────┬───────┬─────────┬──────────┬────────┬─────┐│
│  │Admis │ Student │ Class │ Section │ Fee      │ Total  │ ... ││
│  │sion  │ Name    │       │         │ Group    │ Fee    │     ││
│  ├──────┼─────────┼───────┼─────────┼──────────┼────────┼─────┤│
│  │ 1001 │ John    │ 10    │ A       │ Tuition  │ 50,000 │ ... ││
│  │ 1002 │ Sarah   │ 10    │ A       │ Tuition  │ 50,000 │ ... ││
│  │ 1003 │ Mike    │ 10    │ B       │ Tuition  │ 50,000 │ ... ││
│  │ 1004 │ Emma    │ 9     │ A       │ Transport│ 12,000 │ ... ││
│  │ ...  │ ...     │ ...   │ ...     │ ...      │ ...    │ ... ││
│  └──────┴─────────┴───────┴─────────┴──────────┴────────┴─────┘│
│                                                                  │
│  ... [Continued columns: Collected, Balance, %, Status] ...     │
│                                                                  │
│  Showing 1 to 25 of 150 entries    [◀ 1 2 3 4 5 6 ▶]          │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Full Table Columns**:
1. Admission No
2. Student Name
3. Class
4. Section
5. Fee Group
6. Total Fee (Rs.)
7. Collected (Rs.) - Green text
8. Balance (Rs.) - Red text
9. Collection %
10. Status - Color-coded badges:
    - 🟢 Paid (green badge)
    - 🟡 Partial (yellow badge)
    - 🔴 Pending (red badge)

**Features**:
- Pagination controls
- Entries per page selector (10, 25, 50, 100, All)
- Global search box
- Sortable columns (click headers)
- Export buttons (Excel, CSV)
- Responsive scrolling on mobile

---

## 7. No Data State

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                  │
│                          ℹ️                                      │
│                                                                  │
│              No data available for the                          │
│              selected filters.                                  │
│                                                                  │
│              Please adjust your search criteria.                │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Features**:
- Friendly message
- Info icon
- Helpful suggestion
- Clean, centered layout

---

## Color Scheme

### Primary Colors
- **Purple Gradient**: `#667eea` to `#764ba2` (Summary card)
- **Primary Blue**: `#3c8dbc` (Buttons, links)
- **Success Green**: `#00a65a` (Collected amounts, paid status)
- **Warning Yellow**: `#f39c12` (Partial status)
- **Danger Red**: `#dd4b39` (Balance amounts, pending status)

### Progress Bar Colors
- **Green**: `#5cb85c` (80%+ collection)
- **Yellow**: `#f0ad4e` (50-79% collection)
- **Red**: `#d9534f` (<50% collection)

### Background Colors
- **White**: `#ffffff` (Cards, tables)
- **Light Gray**: `#f8f9fa` (Filter section)
- **Very Light Gray**: `#f0f0f0` (Progress bar background)

---

## Responsive Breakpoints

### Desktop (1400px+)
- 4x4 grid (16 cards)
- Full-width charts side by side
- All columns visible in table

### Laptop (992px - 1399px)
- 3x3 grid (9 cards visible, scroll for more)
- Full-width charts side by side
- All columns visible in table

### Tablet (576px - 991px)
- 2x2 grid (4 cards visible, scroll for more)
- Charts stacked vertically
- Horizontal scroll for table

### Mobile (<576px)
- 1x1 grid (1 card visible, scroll for more)
- Charts stacked vertically
- Horizontal scroll for table
- Simplified filter layout

---

## Interactive Elements

### Hover Effects
1. **Cards**: Lift up 5px with enhanced shadow
2. **Buttons**: Darken background color
3. **Table Rows**: Light gray background
4. **Chart Elements**: Highlight with tooltip

### Click Actions
1. **Search Button**: Load data via AJAX
2. **Export Buttons**: Download file
3. **Table Headers**: Sort column
4. **Pagination**: Navigate pages
5. **Filter Dropdowns**: Open selection menu

### Loading States
1. **Search Button**: Shows spinner icon
2. **Disabled State**: Grayed out during loading
3. **AJAX Calls**: Loading indicator

---

## Accessibility Features

- ✅ Keyboard navigation support
- ✅ Screen reader friendly labels
- ✅ High contrast colors
- ✅ Clear focus indicators
- ✅ Semantic HTML structure
- ✅ ARIA labels where needed

---

## Print Layout

When printing the page:
- Filters section: Hidden
- Summary card: Visible
- Grid: Visible (all 16 cards)
- Charts: Visible
- Table: Visible (all rows, no pagination)
- Export buttons: Hidden
- Page breaks: Optimized

---

## Mobile Experience

### Portrait Mode
- Single column layout
- Stacked sections
- Touch-friendly buttons (min 44px)
- Swipe-friendly charts
- Horizontal scroll for table

### Landscape Mode
- 2-column grid
- Side-by-side charts
- Better table visibility
- Optimized spacing

---

## Animation Effects

1. **Card Hover**: Smooth 0.2s transform
2. **Progress Bars**: Animated fill on load
3. **Charts**: Smooth animation on render
4. **Page Transitions**: Fade in sections
5. **Loading Spinner**: Rotating animation

---

## Browser-Specific Notes

### Chrome/Edge
- Full feature support
- Smooth animations
- Perfect rendering

### Firefox
- Full feature support
- Slightly different date picker style

### Safari
- Full feature support
- Different scrollbar style
- iOS-specific touch optimizations

### Mobile Browsers
- Touch-optimized
- Swipe gestures supported
- Responsive charts

---

This visual guide provides a complete overview of the user interface and user experience of the Fee Group-wise Collection Report.

