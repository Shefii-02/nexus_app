// src/modules/conversations/components/CreateConversationModal.tsx
import { useState } from 'react';

interface Props {
  onClose: () => void;
  onCreate: (userId: number, moduleId?: number) => Promise<void>;
}

export function CreateConversationModal({ onClose, onCreate }: Props) {
  const [userId, setUserId] = useState('');
  const [moduleId, setModuleId] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handle = async () => {
    if (!userId) return;
    setLoading(true);
    setError('');
    try {
      await onCreate(Number(userId), moduleId ? Number(moduleId) : undefined);
      onClose();
    } catch (e: any) {
      setError(e?.response?.data?.message ?? 'Failed to start chat.');
      setLoading(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-box" onClick={e => e.stopPropagation()}>
        <h3>New Chat</h3>
        {error && <p style={{ color: 'var(--danger)', marginBottom: 12 }}>{error}</p>}
        <div className="form-group">
          <label>User ID</label>
          <input
            type="number"
            value={userId}
            onChange={e => setUserId(e.target.value)}
            placeholder="Enter user ID"
            autoFocus
          />
        </div>
        <div className="form-group">
          <label>Module / Course ID <span style={{ color: 'var(--text-secondary)' }}>(optional — isolates chat per course)</span></label>
          <input
            type="number"
            value={moduleId}
            onChange={e => setModuleId(e.target.value)}
            placeholder="e.g. 42"
          />
        </div>
        <div className="modal-actions">
          <button onClick={onClose}>Cancel</button>
          <button className="primary" onClick={handle} disabled={loading || !userId}>
            {loading ? 'Starting...' : 'Start Chat'}
          </button>
        </div>
      </div>
    </div>
  );
}
