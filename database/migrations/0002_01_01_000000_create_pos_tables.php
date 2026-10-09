<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', fn(Blueprint $t) => [$t->id(), $t->string('name')->unique(), $t->boolean('active')->default(true), $t->timestamps()]);
        Schema::create('brands', fn(Blueprint $t) => [$t->id(), $t->string('name')->unique(), $t->boolean('active')->default(true), $t->timestamps()]);
        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $t->string('sku')->unique();
            $t->string('name');
            $t->string('unit')->default('pcs');
            $t->decimal('purchase_price', 15, 2)->default(0);
            $t->decimal('selling_price', 15, 2)->default(0);
            $t->decimal('wholesale_price', 15, 2)->default(0);
            $t->decimal('current_stock', 15, 3)->default(0);
            $t->decimal('minimum_stock', 15, 3)->default(0);
            $t->string('image')->nullable();
            $t->text('description')->nullable();
            $t->string('status')->default('active');
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('product_barcodes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('barcode')->unique();
            $t->boolean('is_primary')->default(false);
            $t->timestamps();
        });
        Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('phone')->nullable()->index();
            $t->string('email')->nullable();
            $t->text('address')->nullable();
            $t->decimal('opening_due', 15, 2)->default(0);
            $t->string('status')->default('active');
            $t->timestamps();
        });
        Schema::create('suppliers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('company')->nullable();
            $t->string('phone')->nullable()->index();
            $t->string('email')->nullable();
            $t->text('address')->nullable();
            $t->decimal('opening_due', 15, 2)->default(0);
            $t->string('status')->default('active');
            $t->timestamps();
        });
        Schema::create('sales', function (Blueprint $t) {
            $t->id();
            $t->string('invoice_no')->unique();
            $t->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->decimal('subtotal', 15, 2);
            $t->decimal('discount', 15, 2)->default(0);
            $t->decimal('tax', 15, 2)->default(0);
            $t->decimal('total', 15, 2);
            $t->decimal('paid', 15, 2)->default(0);
            $t->decimal('due', 15, 2)->default(0);
            $t->decimal('change', 15, 2)->default(0);
            $t->string('payment_method')->default('cash');
            $t->string('status')->default('completed');
            $t->timestamps();
        });
        Schema::create('sale_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->decimal('quantity', 15, 3);
            $t->decimal('unit_price', 15, 2);
            $t->decimal('cost_price', 15, 2);
            $t->decimal('discount', 15, 2)->default(0);
            $t->decimal('tax', 15, 2)->default(0);
            $t->decimal('line_total', 15, 2);
            $t->timestamps();
        });
        Schema::create('purchases', function (Blueprint $t) {
            $t->id();
            $t->string('reference_no')->unique();
            $t->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->decimal('subtotal', 15, 2);
            $t->decimal('discount', 15, 2)->default(0);
            $t->decimal('tax', 15, 2)->default(0);
            $t->decimal('total', 15, 2);
            $t->decimal('paid', 15, 2)->default(0);
            $t->decimal('due', 15, 2)->default(0);
            $t->string('status')->default('received');
            $t->timestamps();
        });
        Schema::create('purchase_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->decimal('quantity', 15, 3);
            $t->decimal('unit_cost', 15, 2);
            $t->decimal('line_total', 15, 2);
            $t->timestamps();
        });
        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->decimal('quantity', 15, 3);
            $t->decimal('previous_stock', 15, 3);
            $t->decimal('new_stock', 15, 3);
            $t->string('movement_type');
            $t->string('reference_type')->nullable();
            $t->unsignedBigInteger('reference_id')->nullable();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->text('description')->nullable();
            $t->timestamps();
            $t->index(['reference_type', 'reference_id']);
        });
        Schema::create('expenses', function (Blueprint $t) {
            $t->id();
            $t->string('category');
            $t->decimal('amount', 15, 2);
            $t->date('expense_date');
            $t->text('note')->nullable();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->timestamps();
        });
        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action');
            $t->string('subject_type')->nullable();
            $t->unsignedBigInteger('subject_id')->nullable();
            $t->string('ip', 45)->nullable();
            $t->text('description')->nullable();
            $t->timestamps();
            $t->index(['subject_type', 'subject_id']);
        });
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        foreach (['settings', 'activity_logs', 'expenses', 'stock_movements', 'purchase_items', 'purchases', 'sale_items', 'sales', 'product_barcodes', 'products', 'suppliers', 'customers', 'brands', 'categories'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
