import { useState } from "react";
import { transactionService } from "../services/transactionService";

export function useCreateTransaction() {
  const [loading, setLoading] = useState(false);

  const createTransaction = async (
    payload: any
  ) => {
    setLoading(true);

    try {
      return await transactionService.create(
        payload
      );
    } finally {
      setLoading(false);
    }
  };

  return {
    loading,
    createTransaction,
  };
}