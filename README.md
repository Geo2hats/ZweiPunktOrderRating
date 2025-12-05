# ZweiPunkt Order Rating Plugin

**Version:** v1.0.3
**License:** MIT
**Type:** Shopware Platform Plugin

## Overview

The ZweiPunkt Order Rating plugin allows customers to rate their orders after purchase. It provides a star rating system with optional comments, stores ratings in order custom fields, and displays a rating interface in the customer account area.

---

## Table of Contents

1. [Order Rating System](#order-rating-system)
2. [Rating Interface](#rating-interface)
3. [Rating Storage & Management](#rating-storage--management)
4. [Custom Fields Integration](#custom-fields-integration)
5. [Configuration Options](#configuration-options)
6. [Technical Details](#technical-details)
7. [Installation & Usage](#installation--usage)

---

## Order Rating System

### 1. Star Rating Functionality
- **Feature:** Star-based rating system for orders
- **Implementation:** `OrderRatingPlugin` JavaScript plugin
- **Functionality:**
  - 1-5 star rating system
  - Visual star selection
  - Rating submission via AJAX
  - Real-time rating display
  - Optional comment field
  - Multi-language support

### 2. Rating Submission
- **Feature:** Save ratings to orders
- **Controller:** `OrderRatingController`
- **Route:** `/order/rating` (POST)
- **Functionality:**
  - Validates rating input
  - Saves rating to order custom fields
  - Stores comment if provided
  - Language-aware rating storage
  - JSON response handling
  - Error handling and validation

---

## Rating Interface

### 3. Rating Form Display
- **Feature:** User-friendly rating interface
- **Implementation:** Storefront JavaScript plugin
- **Functionality:**
  - Star rating input (radio buttons)
  - Comment text area
  - Submit button
  - Form validation
  - Success/error messages
  - Order number display
  - CSRF token protection

### 4. Interactive Rating Experience
- **Feature:** Enhanced user interaction
- **Functionality:**
  - Star selection highlights
  - Form appears when rating is selected
  - Real-time form display
  - Visual feedback
  - Loading states
  - Success confirmation

---

## Rating Storage & Management

### 5. Order Custom Fields
- **Feature:** Store ratings in order custom fields
- **Custom Fields:**
  - `custom_rating_order_review` - Number of stars (integer)
  - `custom_rating_order_review_comment` - Customer comment (text)
- **Functionality:**
  - Automatic field creation during installation
  - Rating value storage (1-5)
  - Comment text storage
  - Order association
  - Multi-language support

### 6. Rating Data Management
- **Feature:** Rating data retrieval and display
- **Functionality:**
  - Load ratings from order custom fields
  - Display in administration
  - Order detail integration
  - Rating statistics
  - Comment viewing

---

## Custom Fields Integration

### 7. Custom Field Definition
- **Feature:** Automatic custom field creation
- **Implementation:** Custom field JSON definition
- **Entity:** Order
- **Fields:**
  - Rating stars (number field, integer)
  - Rating comment (text editor field)
- **Functionality:**
  - Automatic installation
  - Field registration
  - Administration display
  - Multi-language labels

---

## Configuration Options

### 8. System Configuration
- **Location:** Administration → Settings → System → Plugins → ZweiPunkt OrderRating
- **Configuration Options:**
  - Plugin activation settings
  - Rating display options
  - Comment field settings
- **Functionality:**
  - Per-sales-channel configuration
  - Enable/disable rating system
  - Customizable rating interface

---

## Technical Details

### 9. JavaScript Plugin
- **Plugin:** `OrderRatingPlugin`
- **Features:**
  - Star rating selection
  - Form display management
  - AJAX submission
  - Response handling
  - Error management
  - CSRF token handling

### 10. Controller & Routes
- **Controller:** `OrderRatingController`
- **Route:** `/order/rating` (POST, AJAX)
- **Functionality:**
  - Receives rating data
  - Validates input
  - Updates order custom fields
  - Returns JSON response
  - Language handling
  - Error responses

### 11. Subscribers
- **Subscriber:** `AddConfigToView`
- **Functionality:**
  - Adds configuration to view
  - Provides rating settings to templates
  - View data enhancement

### 12. Custom Field Installer
- **Feature:** Automatic custom field installation
- **Implementation:** Plugin installation hook
- **Functionality:**
  - Creates custom field set
  - Registers fields with order entity
  - Sets up field labels
  - Multi-language support

---

## Installation & Usage

### Prerequisites
- Shopware ~6.7.0
- PHP 8.1 or higher

### Installation
1. Install the plugin via Composer or manually
2. Install and activate the plugin in Shopware Administration:
   - Go to Settings → System → Plugins
   - Find "ZweiPunkt OrderRating"
   - Click Install and then Activate

3. Custom fields are automatically created:
   - Rating stars field
   - Rating comment field
   - Order association

4. Configure the plugin:
   - Go to Settings → System → Plugins → ZweiPunkt OrderRating
   - Enable rating functionality
   - Configure display options

### Usage

#### For Administrators:
1. View ratings in order details:
   - Go to Orders → Select order
   - View rating in custom fields section
   - Read customer comments
2. Analyze rating data:
   - Filter orders by rating
   - Export rating statistics
   - Review customer feedback

#### For Customers:
1. Complete an order
2. Navigate to order details in account area
3. See rating interface
4. Select star rating (1-5)
5. Optionally add a comment
6. Submit rating
7. See confirmation message

---

## Rating Features

### Star Rating
- 1-5 star scale
- Visual star selection
- Required field
- Integer storage

### Comments
- Optional text field
- Rich text editor support
- Character limit (if configured)
- Multi-language support

### Display
- Order number display
- Rating summary
- Comment preview
- Success messages

---

## Security Features

- CSRF token protection
- AJAX request validation
- Customer authentication required
- Order ownership verification
- Input sanitization
- XSS protection

---

## Dependencies

- Shopware Core Framework ~6.7.0
- Symfony Components

---

## Support

For questions or support, contact:
- Email: team@zwei.gmbh
- Shopware Store: https://store.shopware.com/zweipunkt-gmbh.html

---

