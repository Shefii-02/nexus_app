

import type { User } from '../services/chatService';
import { getInitials } from '../utils';

interface Props {
  user: User;
  onClose: () => void;
}

export function UserInfoDrawer({ user, onClose }: Props) {
  return (
    <div className="drawer-overlay" onClick={onClose}>
      <div className="drawer" onClick={e => e.stopPropagation()}>
        <div className="drawer-header">
          <button className="icon-btn" onClick={onClose}>✕</button>
          <h3>Contact Info</h3>
        </div>

        <div className="drawer-avatar-section">
          <div className="drawer-avatar">
            {user.avatar ? (
              <img src={user.avatar} alt={user.name} />
            ) : (
              <span className="avatar-initials lg">{getInitials(user.name)}</span>
            )}
          </div>
          <h2>{user.name}</h2>
          {user.email && <p className="user-email">{user.email}</p>}
        </div>

        <div className="drawer-section">
          <div style={{ display: 'flex', flexDirection: 'column', gap: 12, padding: '8px 0' }}>
            <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
              <span style={{ fontSize: 20 }}>👤</span>
              <div>
                <div style={{ fontSize: 13, color: 'var(--text-secondary)' }}>Name</div>
                <div style={{ fontSize: 15 }}>{user.name}</div>
              </div>
            </div>
            {user.email && (
              <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
                <span style={{ fontSize: 20 }}>✉️</span>
                <div>
                  <div style={{ fontSize: 13, color: 'var(--text-secondary)' }}>Email</div>
                  <div style={{ fontSize: 15 }}>{user.email}</div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
