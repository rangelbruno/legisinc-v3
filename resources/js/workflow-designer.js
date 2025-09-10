// Vue Flow Workflow Designer - Professional Implementation
import { createApp, ref, reactive, computed, onMounted, nextTick } from 'vue';
import { VueFlow, Background, Controls, MiniMap } from '@vue-flow/core';

const { createApp: createVueApp, ref: vueRef, reactive: vueReactive, computed: vueComputed, onMounted: vueOnMounted, nextTick: vueNextTick } = Vue;

// Workflow Designer Component
const WorkflowDesigner = {
    template: `
    <div class="workflow-designer">
        <VueFlow 
            v-model:nodes="nodes"
            v-model:edges="edges"
            :nodes-draggable="true"
            :elements-selectable="true"
            :nodes-connectable="true"
            :pan-on-drag="[1, 2]"
            :zoom-on-scroll="true"
            :zoom-on-double-click="false"
            :snap-to-grid="true"
            :snap-grid="[8, 8]"
            :fit-view="true"
            :connection-mode="'loose'"
            :default-edge-options="{ type: 'smoothstep', animated: true, style: { stroke: '#009ef7', strokeWidth: 2 } }"
            @nodes-change="onNodesChange"
            @edges-change="onEdgesChange"
            @connect="onConnect"
            @node-click="onNodeClick"
            @edge-click="onEdgeClick"
            class="workflow-designer"
        >
            <!-- Background with dots pattern -->
            <Background 
                variant="dots" 
                :gap="16" 
                :size="1" 
                color="#e4e6ef"
            />
            
            <!-- Controls in bottom-right corner -->
            <Controls 
                position="bottom-right"
                :show-zoom="true"
                :show-fit-view="true"
                :show-interactive="true"
            />
            
            <!-- MiniMap in top-right corner -->
            <MiniMap 
                :pannable="true" 
                :zoomable="true"
                :node-color="getNodeColor"
                :mask-color="'rgba(255, 255, 255, 0.8)'"
                position="top-right"
                style="width: 200px; height: 120px;"
            />
            
            <!-- Custom Node Template -->
            <template #node-workflow-node="{ data, id }">
                <div 
                    :class="['vue-flow__node-workflow-node', 'node-' + data.tipo]"
                    @dblclick="editNode(id, data)"
                >
                    <div class="node-content">
                        <div class="node-title">{{ data.nome }}</div>
                        <div class="node-subtitle">{{ data.tipo }}</div>
                    </div>
                </div>
            </template>
        </VueFlow>
    </div>
    `,
    
    props: {
        workflowId: {
            type: Number,
            required: true
        },
        workflowName: {
            type: String,
            required: true
        },
        initialData: {
            type: Object,
            default: () => ({})
        }
    },
    
    setup(props, { emit }) {
        // Reactive state
        const nodes = vueRef([]);
        const edges = vueRef([]);
        const selectedNode = vueRef(null);
        const selectedEdge = vueRef(null);
        const nextNodeId = vueRef(1);
        const nextEdgeId = vueRef(1);
        
        // Load workflow data
        const loadWorkflowData = async () => {
            try {
                console.log('Carregando dados do workflow...');
                const response = await fetch(`/admin/workflows/${props.workflowId}/designer-data`);
                const data = await response.json();
                
                if (data.success) {
                    console.log('Dados carregados:', data.data);
                    
                    // Load nodes (etapas)
                    if (data.data.etapas && data.data.etapas.length > 0) {
                        nodes.value = data.data.etapas.map((etapa, index) => ({
                            id: String(etapa.id),
                            type: 'workflow-node',
                            position: { 
                                x: 100 + (index % 3) * 250, 
                                y: 100 + Math.floor(index / 3) * 120 
                            },
                            data: {
                                nome: etapa.nome,
                                tipo: etapa.tipo || 'processo',
                                descricao: etapa.descricao,
                                key: etapa.key
                            }
                        }));
                        
                        nextNodeId.value = Math.max(...data.data.etapas.map(e => e.id)) + 1;
                    }
                    
                    // Load edges (transições)
                    if (data.data.transicoes && data.data.transicoes.length > 0) {
                        edges.value = data.data.transicoes.map(transicao => ({
                            id: String(transicao.id),
                            source: String(transicao.etapa_origem_id),
                            target: String(transicao.etapa_destino_id),
                            type: 'smoothstep',
                            animated: true,
                            data: {
                                condicao: transicao.condicao,
                                tipo: transicao.tipo
                            }
                        }));
                        
                        nextEdgeId.value = Math.max(...data.data.transicoes.map(t => t.id)) + 1;
                    }
                    
                    console.log('Workflow carregado:', { nodes: nodes.value.length, edges: edges.value.length });
                }
            } catch (error) {
                console.error('Erro ao carregar workflow:', error);
            }
        };
        
        // Node color mapping
        const getNodeColor = (node) => {
            const colors = {
                'inicio': '#50cd89',
                'processo': '#009ef7', 
                'decisao': '#ffc700',
                'final': '#f1416c'
            };
            return colors[node.data?.tipo] || '#009ef7';
        };
        
        // Event handlers
        const onNodesChange = (changes) => {
            console.log('Nodes changed:', changes);
        };
        
        const onEdgesChange = (changes) => {
            console.log('Edges changed:', changes);
        };
        
        const onConnect = (params) => {
            console.log('Connecting:', params);
            const newEdge = {
                id: String(nextEdgeId.value++),
                source: params.source,
                target: params.target,
                type: 'smoothstep',
                animated: true,
                data: {}
            };
            edges.value.push(newEdge);
        };
        
        const onNodeClick = (event) => {
            console.log('Node clicked:', event.node);
            selectedNode.value = event.node;
            selectedEdge.value = null;
            updatePropertiesPanel();
        };
        
        const onEdgeClick = (event) => {
            console.log('Edge clicked:', event.edge);
            selectedEdge.value = event.edge;
            selectedNode.value = null;
            updatePropertiesPanel();
        };
        
        const editNode = (nodeId, nodeData) => {
            console.log('Editing node:', nodeId, nodeData);
            // TODO: Open modal or inline editor
        };
        
        const updatePropertiesPanel = () => {
            const panel = document.getElementById('properties-panel');
            if (!panel) return;
            
            if (selectedNode.value) {
                const node = selectedNode.value;
                panel.innerHTML = `
                    <div class="mb-5">
                        <label class="form-label fw-semibold">Nome da Etapa</label>
                        <input type="text" class="form-control" value="${node.data.nome}" />
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-control">
                            <option value="inicio" ${node.data.tipo === 'inicio' ? 'selected' : ''}>Início</option>
                            <option value="processo" ${node.data.tipo === 'processo' ? 'selected' : ''}>Processo</option>
                            <option value="decisao" ${node.data.tipo === 'decisao' ? 'selected' : ''}>Decisão</option>
                            <option value="final" ${node.data.tipo === 'final' ? 'selected' : ''}>Final</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-semibold">Descrição</label>
                        <textarea class="form-control" rows="3">${node.data.descricao || ''}</textarea>
                    </div>
                `;
            } else if (selectedEdge.value) {
                const edge = selectedEdge.value;
                panel.innerHTML = `
                    <div class="mb-5">
                        <label class="form-label fw-semibold">Condição</label>
                        <input type="text" class="form-control" value="${edge.data.condicao || ''}" />
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-control">
                            <option value="sequencial" ${edge.data.tipo === 'sequencial' ? 'selected' : ''}>Sequencial</option>
                            <option value="condicional" ${edge.data.tipo === 'condicional' ? 'selected' : ''}>Condicional</option>
                        </select>
                    </div>
                `;
            } else {
                panel.innerHTML = `
                    <div class="text-center py-8">
                        <i class="ki-duotone ki-information-5 fs-3x text-muted mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <p class="text-muted">Selecione um elemento para editar suas propriedades</p>
                    </div>
                `;
            }
        };
        
        // Auto layout with elkjs
        const autoLayout = async () => {
            if (typeof ELK !== 'undefined') {
                const elk = new ELK({
                    defaultLayoutOptions: {
                        'elk.direction': 'DOWN',
                        'elk.layered.spacing.nodeNodeBetweenLayers': '80',
                        'elk.spacing.nodeNode': '60',
                        'elk.padding': '[20,20,20,20]'
                    }
                });
                
                const graph = {
                    id: 'root',
                    layoutOptions: {},
                    children: nodes.value.map(n => ({ 
                        id: n.id, 
                        width: 180, 
                        height: 80 
                    })),
                    edges: edges.value.map(e => ({ 
                        id: e.id, 
                        sources: [e.source], 
                        targets: [e.target] 
                    }))
                };
                
                try {
                    const { children } = await elk.layout(graph);
                    
                    nodes.value = nodes.value.map(n => {
                        const layoutNode = children.find(c => c.id === n.id);
                        if (layoutNode) {
                            return { 
                                ...n, 
                                position: { 
                                    x: layoutNode.x, 
                                    y: layoutNode.y 
                                } 
                            };
                        }
                        return n;
                    });
                    
                    console.log('Layout automático aplicado');
                } catch (error) {
                    console.error('Erro no layout automático:', error);
                }
            }
        };
        
        // Add new node
        const addNode = (tipo, nome, position = null) => {
            const newNode = {
                id: String(nextNodeId.value++),
                type: 'workflow-node',
                position: position || { 
                    x: Math.random() * 400 + 100, 
                    y: Math.random() * 300 + 100 
                },
                data: {
                    nome: nome || 'Nova Etapa',
                    tipo: tipo,
                    descricao: ''
                }
            };
            
            nodes.value.push(newNode);
            return newNode;
        };
        
        // Clear canvas
        const clearCanvas = () => {
            if (confirm('Tem certeza que deseja limpar o canvas?')) {
                nodes.value = [];
                edges.value = [];
                selectedNode.value = null;
                selectedEdge.value = null;
                updatePropertiesPanel();
            }
        };
        
        // Expose methods to global scope
        window.workflowDesigner = {
            autoLayout,
            addNode,
            clearCanvas,
            nodes,
            edges
        };
        
        // Load data on mount
        vueOnMounted(() => {
            loadWorkflowData();
        });
        
        return {
            nodes,
            edges,
            onNodesChange,
            onEdgesChange,
            onConnect,
            onNodeClick,
            onEdgeClick,
            editNode,
            getNodeColor
        };
    }
};

// Initialize Vue Flow Designer
export function initializeWorkflowDesigner(workflowId, workflowName) {
    // Dynamic import to avoid conflicts
    return import('@vue-flow/core').then(({ VueFlow, Background, Controls, MiniMap }) => {
        const app = createVueApp({
            components: {
                WorkflowDesigner
            },
            setup() {
                return {
                    workflowData: {},
                    workflowId: workflowId,
                    workflowName: workflowName
                };
            }
        });
        
        // Register Vue Flow components globally
        app.component('VueFlow', VueFlow);
        app.component('Background', Background);
        app.component('Controls', Controls);
        app.component('MiniMap', MiniMap);
        
        // Mount the app
        app.mount('#workflow-designer-app');
        
        console.log('Vue Flow Workflow Designer initialized');
        
        return app;
    });
}