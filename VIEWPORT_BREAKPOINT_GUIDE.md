# Viewport Breakpoint Guide - Add Financial Year Page

## Complete Breakpoint Reference

### 1. Mobile Phones (< 768px)
**Examples**: iPhone 12, iPhone 13, iPhone 14, Pixel 6, etc.

```
Viewport Width: 320px - 767px
Layout: SINGLE COLUMN (Stacked)

┌─────────────────────┐
│  Add Financial Year │
├─────────────────────┤
│                     │
│  Form Container     │
│  (100% width)       │
│  - Start Date       │
│  - End Date         │
│  - Save Button      │
│                     │
├─────────────────────┤
│                     │
│  Table Container    │
│  (100% width)       │
│  - Session List     │
│  - Scrollable       │
│                     │
└─────────────────────┘

CSS Applied:
- col-md-4: 100% width
- col-md-8: 100% width
- Padding: 16px
- Gap: 12px
- No horizontal scrolling
```

### 2. Tablets - iPad Mini (768px - 991px)
**Examples**: iPad Mini, iPad Air, iPad (7th gen), etc.

```
Viewport Width: 768px - 991px
Layout: TWO COLUMN (Side-by-side) ✅ NEW

┌──────────────────────────────────────────┐
│  Add Financial Year                      │
├──────────────────────────────────────────┤
│                                          │
│  Form (40%)  │  Table (60%)             │
│  ─────────   │  ──────────────────      │
│  Start Date  │  Session List            │
│  End Date    │  ┌──────────────────┐   │
│  Save Button │  │ Date | Status    │   │
│              │  ├──────────────────┤   │
│              │  │ Data rows...     │   │
│              │  └──────────────────┘   │
│                                          │
└──────────────────────────────────────────┘

CSS Applied:
- col-md-4: 40% width ✅ OPTIMIZED
- col-md-8: 60% width ✅ OPTIMIZED
- Padding: 18px
- Gap: 14px
- Column padding: 8px
- No horizontal scrolling
```

### 3. Large Tablets & Small Desktops (992px - 1199px)
**Examples**: iPad Pro 11", iPad Pro 12.9", Small laptops, etc.

```
Viewport Width: 992px - 1199px
Layout: TWO COLUMN (Side-by-side)

┌────────────────────────────────────────────────────┐
│  Add Financial Year                                │
├────────────────────────────────────────────────────┤
│                                                    │
│  Form (33.33%)  │  Table (66.67%)                │
│  ──────────     │  ──────────────────────────    │
│  Start Date     │  Session List                  │
│  End Date       │  ┌──────────────────────────┐ │
│  Save Button    │  │ Date | Status | Action   │ │
│                 │  ├──────────────────────────┤ │
│                 │  │ Data rows...             │ │
│                 │  └──────────────────────────┘ │
│                                                    │
└────────────────────────────────────────────────────┘

CSS Applied:
- col-md-4: 33.33% width
- col-md-8: 66.67% width
- Padding: 22px
- Gap: 18px
- Column padding: 10px
- No horizontal scrolling
```

### 4. Desktop (1200px+)
**Examples**: Desktop monitors, large laptops, etc.

```
Viewport Width: 1200px+
Layout: TWO COLUMN (Side-by-side)

┌──────────────────────────────────────────────────────────────┐
│  Add Financial Year                                          │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  Form (33.33%)  │  Table (66.67%)                          │
│  ──────────     │  ──────────────────────────────────────  │
│  Start Date     │  Session List                            │
│  End Date       │  ┌──────────────────────────────────────┐│
│  Save Button    │  │ Date | Status | Action | Edit | Del  ││
│                 │  ├──────────────────────────────────────┤│
│                 │  │ Data rows...                         ││
│                 │  └──────────────────────────────────────┘│
│                                                              │
└──────────────────────────────────────────────────────────────┘

CSS Applied:
- col-md-4: 33.33% width
- col-md-8: 66.67% width
- Padding: 28px
- Gap: 20px
- Column padding: 10px
- No horizontal scrolling
```

## Responsive Behavior Summary

| Breakpoint | Device | Width | Layout | col-md-4 | col-md-8 | Padding | Gap |
|-----------|--------|-------|--------|----------|----------|---------|-----|
| Mobile | iPhone, Pixel | <768px | Single | 100% | 100% | 16px | 12px |
| **Tablet** | **iPad Mini/Air** | **768-991px** | **Two-col** | **40%** | **60%** | **18px** | **14px** |
| Large Tablet | iPad Pro 11" | 992-1199px | Two-col | 33.33% | 66.67% | 22px | 18px |
| Desktop | Monitor | 1200px+ | Two-col | 33.33% | 66.67% | 28px | 20px |

## Key Viewport Sizes to Test

### Critical Breakpoints
- **767px** → 768px: Mobile to Tablet transition
- **991px** → 992px: Tablet to Large Tablet transition
- **1199px** → 1200px: Large Tablet to Desktop transition

### Specific Device Widths
- **320px**: iPhone SE
- **375px**: iPhone 12/13/14
- **414px**: iPhone 12/13/14 Plus
- **768px**: iPad Mini (portrait) ✅ **KEY**
- **800px**: iPad Mini (landscape)
- **810px**: iPad Air (portrait)
- **1024px**: iPad Air (landscape) / iPad Pro 11" (portrait)
- **1366px**: iPad Pro 12.9" (landscape)
- **1920px**: Desktop monitor

## Testing Instructions

### Using Chrome DevTools

1. **Open DevTools**: F12 or Ctrl+Shift+I
2. **Toggle Device Toolbar**: Ctrl+Shift+M
3. **Select Device**: Choose "iPad Air" or "iPad Mini"
4. **Verify Layout**: Should show two-column layout
5. **Test Breakpoints**: Manually set width to 768px, 800px, 991px, 992px

### Manual Testing

1. **iPad Air**: Open page in Safari, verify two-column layout
2. **iPad Mini**: Open page in Safari, verify two-column layout
3. **Responsive View**: Test at 768px, 800px, 900px, 991px widths
4. **Scroll**: Verify no horizontal scrolling required
5. **Content**: Verify all form fields and table visible

## Expected Behavior

### At 768px (iPad Mini Portrait)
✅ Two-column layout appears
✅ Form on left (40%), Table on right (60%)
✅ No horizontal scrolling
✅ All content visible

### At 800px (iPad Mini Landscape)
✅ Two-column layout maintained
✅ Better spacing available
✅ No horizontal scrolling
✅ All content visible

### At 991px (Maximum tablet width)
✅ Two-column layout maintained
✅ Optimal spacing for tablets
✅ No horizontal scrolling
✅ All content visible

### At 992px (Minimum large tablet width)
✅ Layout transitions to large tablet
✅ Columns adjust to 33.33% / 66.67%
✅ Spacing increases slightly
✅ No horizontal scrolling

### At 1200px+ (Desktop)
✅ Full desktop layout
✅ Maximum spacing
✅ All features visible
✅ No horizontal scrolling

## Troubleshooting

### If layout is stacked on tablet:
1. Clear browser cache (Ctrl+Shift+Delete)
2. Hard refresh (Ctrl+Shift+R)
3. Check DevTools responsive mode
4. Verify viewport meta tag in HTML

### If content is cut off:
1. Check browser zoom level (should be 100%)
2. Verify no CSS conflicts
3. Check for overflow-x: auto on parent elements
4. Inspect element in DevTools

### If spacing looks wrong:
1. Verify CSS file is loaded (check Network tab)
2. Check for CSS conflicts in other files
3. Verify media query is being applied
4. Check computed styles in DevTools

## Summary

✅ Mobile phones: Single-column layout
✅ iPad Air/Mini: Two-column layout (40% / 60%) ✅ **NEW**
✅ Large tablets: Two-column layout (33.33% / 66.67%)
✅ Desktop: Two-column layout (33.33% / 66.67%)
✅ All sizes: No horizontal scrolling, all content visible

