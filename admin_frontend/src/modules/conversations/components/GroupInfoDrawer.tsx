// src/modules/conversations/components/GroupInfoDrawer.tsx
import { useState } from 'react';
import { getInitials } from '../utils';
import type { Conversation, User } from '../services/chatService';

interface Props {
  conversation: Conversation;
  currentUserId: number;
  onClose: () => void;
}

export function GroupInfoDrawer({ conversation, currentUserId, onClose }: Props) {
  const [leaving, setLeaving] = useState(false);

  const activeParticipants = conversation.participants?.filter(p => p.status === 'active') ?? [];

  const handleLeave = async () => {
    if (!confirm('Leave this group?')) return;
    setLeaving(true);
    try {
      await leaveGroup(conversation.id);
      onClose();
      window.location.reload();
    } catch { setLeaving(false); }
  };

  return (
    <div className="drawer-overlay" onClick={onClose}>
      <div className="drawer" onClick={e => e.stopPropagation()}>
        <div className="drawer-header">
          <button className="icon-btn" onClick={onClose}>✕</button>
          <h3>Group Info</h3>
        </div>

        <div className="drawer-avatar-section">
          <div className="drawer-avatar">
            {conversation.avatar
              ? <img src={conversation.avatar} alt={conversation.title ?? 'Group'} />
              : <span className="avatar-initials lg">{getInitials(conversation.title ?? 'G')}</span>
            }
          </div>
          <h2>{conversation.title}</h2>
          <p className="member-count">{activeParticipants.length} members</p>
        </div>

        <div className="drawer-section">
          <h4>Participants</h4>
          {activeParticipants.map(p => (
            <div key={p.id} className="participant-row">
              <div className="participant-avatar">
                {p.user?.avatar
                  ? <img src={p.user.avatar} alt={p.user.name} />
                  : <span className="avatar-initials sm">{getInitials(p.user?.name ?? '?')}</span>
                }
              </div>
              <div className="participant-info">
                <span className="participant-name">
                  {p.user?.name}
                  {p.user_id === currentUserId && ' (You)'}
                  {p.user_id === conversation.created_by && ' 👑'}
                </span>
                <span className="participant-email">{p.user?.email}</span>
              </div>
            </div>
          ))}
        </div>

        <div className="drawer-footer">
          <button className="danger-btn" onClick={handleLeave} disabled={leaving}>
            {leaving ? 'Leaving...' : 'Leave Group'}
          </button>
        </div>
      </div>
    </div>
  );
}


// ─────────────────────────────────────────────────────────────────────────────
// UserInfoDrawer.tsx
// ─────────────────────────────────────────────────────────────────────────────

interface UserDrawerProps {
  user: User;
  onClose: () => void;
}

export function UserInfoDrawer({ user, onClose }: UserDrawerProps) {
  return (
    <div className="drawer-overlay" onClick={onClose}>
      <div className="drawer" onClick={e => e.stopPropagation()}>
        <div className="drawer-header">
          <button className="icon-btn" onClick={onClose}>✕</button>
          <h3>Contact Info</h3>
        </div>
        <div className="drawer-avatar-section">
          <div className="drawer-avatar">
            {user.avatar
              ? <img src={user.avatar} alt={user.name} />
              : <span className="avatar-initials lg">{getInitials(user.name)}</span>
            }
          </div>
          <h2>{user.name}</h2>
          {user.email && <p className="user-email">{user.email}</p>}
        </div>
      </div>
    </div>
  );
}


// ─────────────────────────────────────────────────────────────────────────────
// CreateConversationModal.tsx
// ─────────────────────────────────────────────────────────────────────────────
interface CreateConvProps {
  onClose: () => void;
  onCreate: (userId: number, moduleId?: number) => Promise<void>;
}

export function CreateConversationModal({ onClose, onCreate }: CreateConvProps) {
  const [userId, setUserId] = useState('');
  const [moduleId, setModuleId] = useState('');
  const [loading, setLoading] = useState(false);

  const handle = async () => {
    if (!userId) return;
    setLoading(true);
    try {
      await onCreate(Number(userId), moduleId ? Number(moduleId) : undefined);
      onClose();
    } catch { setLoading(false); }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-box" onClick={e => e.stopPropagation()}>
        <h3>New Chat</h3>
        <div className="form-group">
          <label>User ID</label>
          <input type="number" value={userId} onChange={e => setUserId(e.target.value)} placeholder="Enter user ID" />
        </div>
        <div className="form-group">
          <label>Module/Course ID (optional)</label>
          <input type="number" value={moduleId} onChange={e => setModuleId(e.target.value)} placeholder="For context isolation" />
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


// ─────────────────────────────────────────────────────────────────────────────
// CreateGroupModal.tsx
// ─────────────────────────────────────────────────────────────────────────────
interface CreateGroupProps {
  onClose: () => void;
  onCreate: (title: string, userIds: number[], moduleId?: number) => Promise<void>;
}

export function CreateGroupModal({ onClose, onCreate }: CreateGroupProps) {
  const [title, setTitle] = useState('');
  const [userIdsStr, setUserIdsStr] = useState('');
  const [moduleId, setModuleId] = useState('');
  const [loading, setLoading] = useState(false);

  const handle = async () => {
    const userIds = userIdsStr.split(',').map(s => Number(s.trim())).filter(Boolean);
    if (!title || userIds.length < 2) return;
    setLoading(true);
    try {
      await onCreate(title, userIds, moduleId ? Number(moduleId) : undefined);
      onClose();
    } catch { setLoading(false); }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-box" onClick={e => e.stopPropagation()}>
        <h3>New Group</h3>
        <div className="form-group">
          <label>Group Name</label>
          <input value={title} onChange={e => setTitle(e.target.value)} placeholder="Group name" />
        </div>
        <div className="form-group">
          <label>Participant IDs (comma-separated)</label>
          <input value={userIdsStr} onChange={e => setUserIdsStr(e.target.value)} placeholder="1,2,3,4" />
        </div>
        <div className="form-group">
          <label>Module/Course ID (optional)</label>
          <input type="number" value={moduleId} onChange={e => setModuleId(e.target.value)} />
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
