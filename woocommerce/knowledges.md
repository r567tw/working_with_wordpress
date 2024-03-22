
# $gateway->supports
- tokenization: 允許使用者在「我的帳號」增加付款方式


# WooCommerce 訂單創建流程
1. 訂單建立（存進數據庫）：客戶完成結賬信息並提交訂單後，WooCommerce 先將訂單信息存儲進數據庫。
2. woocommerce_checkout_order_processed 鉤子觸發
3. $gateway->process_payment() 


#  $gateway->process_payment v.s woocommerce_checkout_order_processed
## WooCommerce checkout_order_processed
- 觸發時機：此鉤子在訂單數據被處理並存入數據庫之後觸發，但在支付過程之前。它允許開發者在訂單被最終確認前對訂單數據進行訪問或修改。
- 用途：主要用於訪問和操作訂單數據，例如添加自定義訂單元數據、執行基於訂單信息的自定義動作等。適合於不需要改變支付流程，但需要在訂單創建後立即執行操作的場景。

## $gateway->process_payment()
- 觸發時機：process_payment 方法在訂單提交到 Gateway 進行支付處理時被調用。它是整個支付流程的核心，負責處理支付邏輯，包括與支付服務提供商的通信。
- 用途：用於實現具體的支付邏輯，例如發送請求到支付服務提供商、處理響應、設置訂單狀態為已支付或支付失敗等。是自定義支付網關或修改現有支付流程的關鍵點。

## Summary
- 流程差異：checkout_order_processed 主要關注訂單的創建和處理，而 process_payment 則專注於支付過程的具體實現。前者在訂單準備進入支付流程之前被觸發，後者則是在實際進行支付處理時觸發。
- 使用場景：如果目標是在訂單被創建後立即執行某些非支付相關的操作（比如自定義日誌記錄、發送自定義通知等），則 checkout_order_processed 是更合適的選擇。若需要定制或干預實際的支付處理邏輯（如添加特定的支付參數、處理支付響應等），則應該使用 process_payment 方法。

