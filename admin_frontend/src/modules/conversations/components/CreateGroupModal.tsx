// src/modules/conversations/components/CreateGroupModal.tsx
import { useState } from 'react';

interface Props {
  onClose: () => void;
  onCreate: (title: string, userIds: number[], moduleId?: number) => Promise<void>;
}

export function CreateGroupModal({ onClose, onCreate }: Props) {
  const [title, setTitle]         = useState('');
  const [userIdsStr, setUserIdsStr] = useState('');
  const [moduleId, setModuleId]   = useState('');
  const [loading, setLoading]     = useState(false);
  const [error, setError]         = useState('');

  const handle = async () => {
    const userIds = userIdsStr
      .split(',')
      .map(s => Number(s.trim()))
      .filter(n => !isNaN(n) && n > 0);

    if (!title.trim()) { setError('Group name is required.'); return; }
    if (userIds.length < 2) { setError('Add at least 2 participants.'); return; }

    setLoading(true);
    setError('');
    try {
      await onCreate(title.trim(), userIds, moduleId ? Number(moduleId) : undefined);
      onClose();
    } catch (e: any) {
      setError(e?.response?.data?.message ?? 'Failed to create group.');
      setLoading(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-box" onClick={e => e.stopPropagation()}>
        <h3>New Group</h3>
        {error && <p style={{ color: 'var(--danger)', marginBottom: 12 }}>{error}</p>}

        <div className="form-group">
          <label>Group Name</label>
          <input
            value={title}
            onChange={e => setTitle(e.target.value)}
            placeholder="e.g. Physics Class 2024"
            autoFocus
          />
        </div>

        <div className="form-group">
          <label>Participant IDs <span style={{ color: 'var(--text-secondary)' }}>(comma-separated)</span></label>
          <input
            value={userIdsStr}
            onChange={e => setUserIdsStr(e.target.value)}
            placeholder="1, 5, 12, 33"
          />
        </div>

        <div className="form-group">
          <label>Module / Course ID <span style={{ color: 'var(--text-secondary)' }}>(optional)</span></label>
          <input
            type="number"
            value={moduleId}
            onChange={e => setModuleId(e.target.value)}
            placeholder="e.g. 42"
          />
        </div>

        <div className="modal-actions">
          <button onClick={onClose}>Cancel</button>
          <button className="primary" onClick={handle} disabled={loading || !title}>
            {loading ? 'Creating...' : 'Create Group'}
          </button>
        </div>
      </div>
    </div>
  );
}
