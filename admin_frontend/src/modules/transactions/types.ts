export interface Transaction {
  id: number;
  transaction_no: string;
  type: "income" | "expense" | "refund";
  category: string;
  payment_method: string;
  description: string;
  amount: number;
  transaction_date: string;
}

export interface TransactionResponse {
  data: Transaction[];
  total: number;
  current_page: number;
  last_page: number;
}