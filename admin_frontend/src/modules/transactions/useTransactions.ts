import { useCallback, useEffect, useState } from "react";
import { transactionService } from "../services/transactionService";

export function useTransactions(filters: any) {
  const [rows, setRows] = useState([]);
  const [meta, setMeta] = useState({
    total: 0,
    current_page: 1,
    last_page: 1,
  });

  const [loading, setLoading] = useState(false);

  const fetchTransactions = useCallback(async () => {
    setLoading(true);

    try {
      const res = await transactionService.getAll(filters);

      setRows(res.data ?? []);

      setMeta({
        total: res.total,
        current_page: res.current_page,
        last_page: res.last_page,
      });
    } finally {
      setLoading(false);
    }
  }, [filters]);

  useEffect(() => {
    fetchTransactions();
  }, [fetchTransactions]);

  return {
    rows,
    meta,
    loading,
    refresh: fetchTransactions,
  };
}