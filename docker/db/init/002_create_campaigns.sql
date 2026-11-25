CREATE TABLE campaigns (
    id BIGSERIAL PRIMARY KEY,
    dm_id BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    name VARCHAR(120) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    room_code VARCHAR(16) NOT NULL UNIQUE,
    status VARCHAR(20) NOT NULL DEFAULT 'active'
        CHECK (status IN ('planned', 'active', 'archived')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_campaigns_dm_id ON campaigns(dm_id);
