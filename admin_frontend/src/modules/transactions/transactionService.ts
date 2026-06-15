import axios from "@/lib/axios";

export interface TransactionFilters {
  type?: string;
  search?: string;
  from?: string;
  to?: string;
  page?: number;
  per_page?: number;
}

export const transactionService = {
  async getAll(filters: TransactionFilters) {
    const { data } = await axios.get("/transactions", {
      params: filters,
    });

    return data;
  },

  async create(payload: any) {
    const { data } = await axios.post(
      "/transactions",
      payload
    );

    return data;
  },

  async getSummary(filters: TransactionFilters = {}) {
    const { data } = await axios.get(
      "/transactions/summary",
      {
        params: filters,
      }
    );

    return data;
  },
};