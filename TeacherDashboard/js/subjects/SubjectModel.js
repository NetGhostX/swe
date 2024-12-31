export class SubjectModel {
    constructor(data) {
        this.id = data.id || crypto.randomUUID();
        this.name = data.name;
        this.description = data.description || '';
        this.color = data.color;
        this.icon = data.icon;
        this.createdAt = data.createdAt || new Date().toISOString();
        this.updatedAt = new Date().toISOString();
        this.topics = data.topics || [];
        this.resources = data.resources || [];
        this.metadata = {
            lastAccessed: new Date().toISOString(),
            topicCount: 0,
            resourceCount: 0
        };
        this.content = data.content || '';
        this.materials = data.materials || [];
    }

    validate() {
        const required = ['name', 'color', 'icon'];
        const missing = required.filter(field => !this[field]);
        
        if (missing.length > 0) {
            throw new Error(`Missing required fields: ${missing.join(', ')}`);
        }
        return true;
    }

    toJSON() {
        return {
            id: this.id,
            name: this.name,
            description: this.description,
            color: this.color,
            icon: this.icon,
            createdAt: this.createdAt,
            updatedAt: this.updatedAt,
            topics: this.topics,
            resources: this.resources,
            metadata: this.metadata,
            content: this.content,
            materials: this.materials
        };
    }
}
