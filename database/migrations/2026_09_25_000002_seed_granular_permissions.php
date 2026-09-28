<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
 public function up(): void {
  $items=[
   ['Expense View','expenses.view','Expenses'],['Expense Add','expenses.create','Expenses'],['Expense Edit','expenses.edit','Expenses'],['Expense Delete','expenses.delete','Expenses'],
   ['Supplier View','suppliers.view','Suppliers'],['Supplier Add','suppliers.create','Suppliers'],['Supplier Edit','suppliers.edit','Suppliers'],['Supplier Delete','suppliers.delete','Suppliers'],['Supplier Payment','suppliers.payment','Suppliers'],
   ['Customer View','customers.view','Customers'],['Customer Add','customers.create','Customers'],['Customer Edit','customers.edit','Customers'],['Customer Delete','customers.delete','Customers'],['Customer Payment','customers.payment','Customers'],
   ['Product Archive','products.archive','Products'],['Product Restore','products.restore','Products'],['User View','users.view','System'],['User Add','users.create','System'],['User Edit','users.edit','System'],['User Delete','users.delete','System'],
  ]; foreach($items as [$name,$slug,$group]) DB::table('permissions')->updateOrInsert(['slug'=>$slug],['name'=>$name,'group'=>$group,'updated_at'=>now(),'created_at'=>now()]);
  $admin=DB::table('roles')->where('slug','admin')->value('id'); $manager=DB::table('roles')->where('slug','manager')->value('id'); $cashier=DB::table('roles')->where('slug','cashier')->value('id');
  $all=DB::table('permissions')->pluck('id'); if($admin) foreach($all as $pid) DB::table('permission_role')->updateOrInsert(['permission_id'=>$pid,'role_id'=>$admin],[]);
  $managerSlugs=['expenses.view','expenses.create','expenses.edit','suppliers.view','suppliers.create','suppliers.edit','suppliers.payment','customers.view','customers.create','customers.edit','customers.payment','products.archive','products.restore'];
  if($manager) foreach(DB::table('permissions')->whereIn('slug',$managerSlugs)->pluck('id') as $pid) DB::table('permission_role')->updateOrInsert(['permission_id'=>$pid,'role_id'=>$manager],[]);
  $cashierSlugs=['expenses.view','expenses.create','suppliers.view','customers.view','customers.create','customers.payment'];
  if($cashier) foreach(DB::table('permissions')->whereIn('slug',$cashierSlugs)->pluck('id') as $pid) DB::table('permission_role')->updateOrInsert(['permission_id'=>$pid,'role_id'=>$cashier],[]);
 }
 public function down(): void {DB::table('permissions')->whereIn('slug',['expenses.view','expenses.create','expenses.edit','expenses.delete','suppliers.view','suppliers.create','suppliers.edit','suppliers.delete','suppliers.payment','customers.view','customers.create','customers.edit','customers.delete','customers.payment','products.archive','products.restore'])->delete();}
};
