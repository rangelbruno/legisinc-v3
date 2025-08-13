@extends('components.layouts.app')

@section('title', 'Diagnóstico do Banco de Dados')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-abstract-26 fs-2 me-3 text-primary">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Diagrama do Banco de Dados
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.system-diagnostic.index') }}" class="text-muted text-hover-primary">Diagnóstico</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Banco de Dados</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Secondary button-->
                <a href="{{ route('admin.system-diagnostic.index') }}" class="btn btn-sm btn-flex btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                <!--end::Secondary button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!--begin::Alert-->
            <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div class="d-flex flex-column">
                    <h5 class="mb-1">Diagrama Interativo</h5>
                    <span>Encontradas {{ count($tables) }} tabelas e {{ count($relationships) }} relacionamentos.</span>
                </div>
            </div>
            <!--end::Alert-->
            
            <!-- Diagrama Teste -->
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-12">
                    <div class="card card-flush">
                        <div class="card-header pt-8">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-800 fs-2">
                                    <i class="ki-duotone ki-abstract-26 fs-1 text-success me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Diagrama Interativo
                                </h3>
                            </div>
                            <div class="card-toolbar">
                                <div class="btn-group me-2">
                                    <button id="zoom-in" class="btn btn-sm btn-light" title="Zoom In">
                                        <i class="ki-duotone ki-plus fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                    <button id="zoom-out" class="btn btn-sm btn-light" title="Zoom Out">
                                        <i class="ki-duotone ki-minus fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                    <button id="zoom-reset" class="btn btn-sm btn-light" title="Reset Zoom">
                                        <i class="ki-duotone ki-abstract-35 fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                </div>
                                <button id="clear-highlight" class="btn btn-sm btn-secondary me-2">
                                    <i class="ki-duotone ki-eraser fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Limpar Destaque
                                </button>
                                <button id="manual-test" class="btn btn-sm btn-primary">
                                    <i class="ki-duotone ki-gear fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Recarregar
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-4">
                                <strong>Status:</strong> <span id="test-status">Aguardando...</span>
                            </div>
                            
                            <div id="test-diagram" style="width: 100%; height: 400px; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 8px; position: relative;">
                                <div id="loading-test" class="d-flex justify-content-center align-items-center h-100" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; background: rgba(248, 249, 250, 0.9);">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                        <div class="mt-3 text-gray-600">Testando D3.js...</div>
                                    </div>
                                </div>
                                <svg id="test-svg" style="display: none; width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 5;"></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->

    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Teste D3.js iniciando...');
            
            // Verificar se D3 está carregado
            if (typeof d3 === 'undefined') {
                console.error('❌ D3.js não foi carregado!');
                document.getElementById('loading-test').innerHTML = '<div class="text-center"><h5 class="text-danger">Erro: D3.js não carregado</h5></div>';
                return;
            }
            
            console.log('✅ D3 carregado, versão:', d3.version);
            
            const tables = @json($tables);
            const relationships = @json($relationships);
            
            console.log('📊 Tables:', tables.length);
            console.log('🔗 Relationships:', relationships.length);
            console.log('📋 Sample table:', tables[0]);
            console.log('🔗 Sample relationship:', relationships[0]);
            
            const container = document.getElementById('test-diagram');
            const width = container.clientWidth || 800;
            const height = 400;
            
            console.log('📐 Dimensões:', width, 'x', height);
            
            try {
                // Criar SVG com zoom e pan
                const svg = d3.select('#test-svg')
                    .attr('width', width)
                    .attr('height', height);

                // Criar grupo principal para zoom/pan
                const mainGroup = svg.append('g')
                    .attr('class', 'main-group');

                // Configurar zoom
                const zoom = d3.zoom()
                    .scaleExtent([0.1, 4])
                    .on('zoom', function(event) {
                        mainGroup.attr('transform', event.transform);
                    });

                svg.call(zoom);
                
                console.log('✅ SVG criado');
                
                // Verificar se temos dados válidos
                if (!tables || tables.length === 0) {
                    console.error('❌ Nenhuma tabela encontrada');
                    throw new Error('Nenhuma tabela encontrada');
                }

                // Filtrar relacionamentos válidos
                const validRelationships = relationships.filter(rel => 
                    rel.from_table && rel.to_table &&
                    tables.some(t => t.name === rel.from_table) &&
                    tables.some(t => t.name === rel.to_table)
                );

                console.log('✅ Relacionamentos válidos:', validRelationships.length);

                // Transformar relacionamentos para formato D3
                const d3Links = validRelationships.map(rel => ({
                    source: rel.from_table,
                    target: rel.to_table,
                    constraint_name: rel.constraint_name
                }));

                console.log('🔗 Links formatados:', d3Links.length);

                // Criar simulação de força para organizar as tabelas
                const simulation = d3.forceSimulation(tables)
                    .force('link', d3.forceLink(d3Links).id(d => d.name))
                    .force('charge', d3.forceManyBody().strength(-400))
                    .force('center', d3.forceCenter(width / 2, height / 2))
                    .force('collision', d3.forceCollide().radius(90));

                // Criar grupo para as conexões (linhas)
                const linkElements = mainGroup.append('g')
                    .attr('class', 'links')
                    .selectAll('line')
                    .data(d3Links)
                    .enter().append('line')
                    .attr('stroke', '#999')
                    .attr('stroke-opacity', 0.6)
                    .attr('stroke-width', 2);

                // Criar grupo para as tabelas (nós)
                const nodes = mainGroup.append('g')
                    .attr('class', 'nodes')
                    .selectAll('g')
                    .data(tables)
                    .enter().append('g')
                    .attr('class', 'table-node')
                    .call(d3.drag()
                        .on('start', function(event, d) {
                            if (!event.active) simulation.alphaTarget(0.3).restart();
                            d.fx = d.x;
                            d.fy = d.y;
                        })
                        .on('drag', function(event, d) {
                            d.fx = event.x;
                            d.fy = event.y;
                        })
                        .on('end', function(event, d) {
                            if (!event.active) simulation.alphaTarget(0);
                            d.fx = null;
                            d.fy = null;
                        }));

                // Adicionar gradiente para os nós
                const defs = svg.append('defs');
                
                const gradient = defs.append('linearGradient')
                    .attr('id', 'tableGradient')
                    .attr('x1', '0%')
                    .attr('y1', '0%')
                    .attr('x2', '0%')
                    .attr('y2', '100%');

                gradient.append('stop')
                    .attr('offset', '0%')
                    .attr('stop-color', '#4a90e2')
                    .attr('stop-opacity', 1);

                gradient.append('stop')
                    .attr('offset', '100%')
                    .attr('stop-color', '#357abd')
                    .attr('stop-opacity', 1);

                // Gradiente para tabela selecionada (vermelho)
                const selectedGradient = defs.append('linearGradient')
                    .attr('id', 'tableGradientSelected')
                    .attr('x1', '0%')
                    .attr('y1', '0%')
                    .attr('x2', '0%')
                    .attr('y2', '100%');

                selectedGradient.append('stop')
                    .attr('offset', '0%')
                    .attr('stop-color', '#ff6b6b')
                    .attr('stop-opacity', 1);

                selectedGradient.append('stop')
                    .attr('offset', '100%')
                    .attr('stop-color', '#e55252')
                    .attr('stop-opacity', 1);

                // Gradiente para tabelas relacionadas (verde)
                const highlightGradient = defs.append('linearGradient')
                    .attr('id', 'tableGradientHighlight')
                    .attr('x1', '0%')
                    .attr('y1', '0%')
                    .attr('x2', '0%')
                    .attr('y2', '100%');

                highlightGradient.append('stop')
                    .attr('offset', '0%')
                    .attr('stop-color', '#28a745')
                    .attr('stop-opacity', 1);

                highlightGradient.append('stop')
                    .attr('offset', '100%')
                    .attr('stop-color', '#1e7e34')
                    .attr('stop-opacity', 1);

                // Gradiente para tabelas dimmed (cinza)
                const dimmedGradient = defs.append('linearGradient')
                    .attr('id', 'tableGradientDimmed')
                    .attr('x1', '0%')
                    .attr('y1', '0%')
                    .attr('x2', '0%')
                    .attr('y2', '100%');

                dimmedGradient.append('stop')
                    .attr('offset', '0%')
                    .attr('stop-color', '#6c757d')
                    .attr('stop-opacity', 1);

                dimmedGradient.append('stop')
                    .attr('offset', '100%')
                    .attr('stop-color', '#5a6268')
                    .attr('stop-opacity', 1);

                // Adicionar sombra
                const dropShadow = defs.append('filter')
                    .attr('id', 'dropShadow')
                    .attr('x', '-20%')
                    .attr('y', '-20%')
                    .attr('width', '140%')
                    .attr('height', '140%');

                dropShadow.append('feDropShadow')
                    .attr('dx', 2)
                    .attr('dy', 2)
                    .attr('stdDeviation', 3)
                    .attr('flood-color', '#000')
                    .attr('flood-opacity', 0.3);

                // Adicionar retângulos para as tabelas com melhor design
                nodes.append('rect')
                    .attr('width', 140)
                    .attr('height', 70)
                    .attr('rx', 10)
                    .attr('fill', 'url(#tableGradient)')
                    .attr('stroke', '#2c5aa0')
                    .attr('stroke-width', 2)
                    .attr('filter', 'url(#dropShadow)')
                    .style('cursor', 'move')
                    .on('mouseover', function(event, d) {
                        d3.select(this)
                            .transition()
                            .duration(200)
                            .attr('stroke-width', 3)
                            .attr('stroke', '#ffc107');
                        
                        // Mostrar tooltip
                        const tooltip = d3.select('#tooltip');
                        if (tooltip.empty()) {
                            d3.select('body').append('div')
                                .attr('id', 'tooltip')
                                .style('position', 'absolute')
                                .style('background', 'rgba(0,0,0,0.9)')
                                .style('color', 'white')
                                .style('padding', '8px 12px')
                                .style('border-radius', '6px')
                                .style('font-size', '12px')
                                .style('pointer-events', 'none')
                                .style('opacity', 0)
                                .style('z-index', '1000');
                        }
                        
                        d3.select('#tooltip')
                            .html(`
                                <strong>${d.name}</strong><br/>
                                Registros: ${d.rows}<br/>
                                Tamanho: ${d.size}<br/>
                                Engine: ${d.engine}
                            `)
                            .style('left', (event.pageX + 10) + 'px')
                            .style('top', (event.pageY - 10) + 'px')
                            .transition()
                            .duration(200)
                            .style('opacity', 1);
                    })
                    .on('mouseout', function() {
                        d3.select(this)
                            .transition()
                            .duration(200)
                            .attr('stroke-width', 2)
                            .attr('stroke', '#2c5aa0');
                        
                        // Esconder tooltip
                        d3.select('#tooltip').style('opacity', 0);
                    })
                    .on('click', function(event, d) {
                        event.stopPropagation();
                        highlightRelatedTables(d.name);
                    });

                // Adicionar ícone para a tabela
                nodes.append('text')
                    .attr('x', 15)
                    .attr('y', 25)
                    .attr('text-anchor', 'middle')
                    .attr('fill', 'white')
                    .attr('font-size', '16px')
                    .attr('font-family', 'Font Awesome 6 Free')
                    .attr('font-weight', '900')
                    .text('📋');

                // Adicionar nome da tabela com quebra de linha se necessário
                nodes.append('text')
                    .attr('x', 70)
                    .attr('y', 20)
                    .attr('text-anchor', 'middle')
                    .attr('fill', 'white')
                    .attr('font-size', '11px')
                    .attr('font-weight', 'bold')
                    .attr('font-family', 'Arial, sans-serif')
                    .each(function(d) {
                        const text = d3.select(this);
                        const tableName = d.name;
                        
                        if (tableName.length > 15) {
                            // Quebrar nome longo em duas linhas
                            const words = tableName.split('_');
                            if (words.length > 1) {
                                const midPoint = Math.ceil(words.length / 2);
                                const firstLine = words.slice(0, midPoint).join('_');
                                const secondLine = words.slice(midPoint).join('_');
                                
                                text.append('tspan')
                                    .attr('x', 70)
                                    .attr('dy', 0)
                                    .text(firstLine);
                                
                                text.append('tspan')
                                    .attr('x', 70)
                                    .attr('dy', 12)
                                    .text(secondLine);
                            } else {
                                text.text(tableName.substring(0, 15) + '...');
                            }
                        } else {
                            text.text(tableName);
                        }
                    });

                // Adicionar número de registros com ícone
                nodes.append('text')
                    .attr('x', 70)
                    .attr('y', 55)
                    .attr('text-anchor', 'middle')
                    .attr('fill', 'rgba(255,255,255,0.9)')
                    .attr('font-size', '9px')
                    .attr('font-family', 'Arial, sans-serif')
                    .text(d => `📊 ${d.rows} registros`);

                // Atualizar posições durante a simulação
                simulation.on('tick', function() {
                    linkElements
                        .attr('x1', d => (d.source.x || 0) + 70)
                        .attr('y1', d => (d.source.y || 0) + 35)
                        .attr('x2', d => (d.target.x || 0) + 70)
                        .attr('y2', d => (d.target.y || 0) + 35);

                    nodes
                        .attr('transform', d => `translate(${(d.x || 0) - 70},${(d.y || 0) - 35})`);
                });
                
                console.log('✅ Elementos adicionados');
                
                // Função para destacar tabelas relacionadas
                function highlightRelatedTables(selectedTableName) {
                    console.log('🎯 Destacando relacionamentos para:', selectedTableName);
                    
                    // Encontrar todas as tabelas relacionadas
                    const relatedTables = new Set();
                    relatedTables.add(selectedTableName); // Incluir a própria tabela
                    
                    d3Links.forEach(link => {
                        if (link.source === selectedTableName || (typeof link.source === 'object' && link.source.name === selectedTableName)) {
                            const targetName = typeof link.target === 'object' ? link.target.name : link.target;
                            relatedTables.add(targetName);
                        }
                        if (link.target === selectedTableName || (typeof link.target === 'object' && link.target.name === selectedTableName)) {
                            const sourceName = typeof link.source === 'object' ? link.source.name : link.source;
                            relatedTables.add(sourceName);
                        }
                    });
                    
                    console.log('📋 Tabelas relacionadas:', Array.from(relatedTables));
                    
                    // Resetar todos os nós primeiro
                    nodes.selectAll('rect')
                        .transition()
                        .duration(300)
                        .attr('opacity', 0.3)
                        .attr('fill', 'url(#tableGradientDimmed)')
                        .attr('stroke', '#999');
                    
                    // Destacar nós relacionados
                    nodes.selectAll('rect')
                        .filter(d => relatedTables.has(d.name))
                        .transition()
                        .duration(300)
                        .attr('opacity', 1)
                        .attr('fill', d => d.name === selectedTableName ? 'url(#tableGradientSelected)' : 'url(#tableGradientHighlight)')
                        .attr('stroke', d => d.name === selectedTableName ? '#ff6b6b' : '#28a745')
                        .attr('stroke-width', 3);
                    
                    // Destacar links relacionados
                    linkElements
                        .transition()
                        .duration(300)
                        .attr('opacity', d => {
                            const sourceName = typeof d.source === 'object' ? d.source.name : d.source;
                            const targetName = typeof d.target === 'object' ? d.target.name : d.target;
                            return (relatedTables.has(sourceName) && relatedTables.has(targetName)) ? 1 : 0.1;
                        })
                        .attr('stroke', d => {
                            const sourceName = typeof d.source === 'object' ? d.source.name : d.source;
                            const targetName = typeof d.target === 'object' ? d.target.name : d.target;
                            return (relatedTables.has(sourceName) && relatedTables.has(targetName)) ? '#28a745' : '#999';
                        })
                        .attr('stroke-width', d => {
                            const sourceName = typeof d.source === 'object' ? d.source.name : d.source;
                            const targetName = typeof d.target === 'object' ? d.target.name : d.target;
                            return (relatedTables.has(sourceName) && relatedTables.has(targetName)) ? 3 : 2;
                        });
                }
                
                // Função para limpar destaque
                function clearHighlight() {
                    console.log('🧹 Limpando destaque');
                    
                    // Resetar todos os nós
                    nodes.selectAll('rect')
                        .transition()
                        .duration(300)
                        .attr('opacity', 1)
                        .attr('fill', 'url(#tableGradient)')
                        .attr('stroke', '#2c5aa0')
                        .attr('stroke-width', 2);
                    
                    // Resetar todos os links
                    linkElements
                        .transition()
                        .duration(300)
                        .attr('opacity', 0.6)
                        .attr('stroke', '#999')
                        .attr('stroke-width', 2);
                }
                
                // Adicionar evento de clique no fundo para limpar destaque
                svg.on('click', function() {
                    clearHighlight();
                });
                
                // Adicionar controles de zoom
                document.getElementById('zoom-in').addEventListener('click', () => {
                    svg.transition().duration(300).call(
                        zoom.scaleBy, 1.5
                    );
                });

                document.getElementById('zoom-out').addEventListener('click', () => {
                    svg.transition().duration(300).call(
                        zoom.scaleBy, 1 / 1.5
                    );
                });

                document.getElementById('zoom-reset').addEventListener('click', () => {
                    svg.transition().duration(500).call(
                        zoom.transform,
                        d3.zoomIdentity
                    );
                });

                // Adicionar evento para botão de limpar destaque
                document.getElementById('clear-highlight').addEventListener('click', () => {
                    clearHighlight();
                });
                
                // Atualizar status
                document.getElementById('test-status').textContent = 'Diagrama carregado! Clique em uma tabela para destacar relacionamentos. Use zoom e arraste as tabelas.';
                
                // Mostrar resultado após 2 segundos
                setTimeout(() => {
                    console.log('🎯 Mostrando resultado...');
                    
                    const loadingEl = document.getElementById('loading-test');
                    const svgEl = document.getElementById('test-svg');
                    
                    console.log('Loading element:', loadingEl);
                    console.log('SVG element:', svgEl);
                    
                    if (loadingEl) {
                        loadingEl.style.display = 'none';
                        loadingEl.style.visibility = 'hidden';
                        loadingEl.style.opacity = '0';
                        console.log('✅ Loading ocultado');
                    }
                    
                    if (svgEl) {
                        svgEl.style.display = 'block';
                        svgEl.style.visibility = 'visible';
                        svgEl.style.opacity = '1';
                        svgEl.style.zIndex = '10';
                        console.log('✅ SVG mostrado');
                    }
                    
                    // Verificar se os elementos foram criados
                    const circle = svgEl ? svgEl.querySelector('circle') : null;
                    const text = svgEl ? svgEl.querySelector('text') : null;
                    
                    console.log('Círculo criado:', circle);
                    console.log('Texto criado:', text);
                    
                    document.getElementById('test-status').textContent = `Teste concluído! Círculo: ${circle ? 'Criado' : 'Não encontrado'}, Texto: ${text ? 'Criado' : 'Não encontrado'}`;
                    console.log('✅ Teste concluído com sucesso!');
                    
                    // Teste adicional - forçar visibilidade
                    setTimeout(() => {
                        const container = document.getElementById('test-diagram');
                        const loading = document.getElementById('loading-test');
                        const svg = document.getElementById('test-svg');
                        
                        console.log('🔧 Teste adicional...');
                        console.log('Container:', container);
                        console.log('Loading display:', loading ? loading.style.display : 'null');
                        console.log('SVG display:', svg ? svg.style.display : 'null');
                        
                        // Forçar ocultação do loading
                        if (loading) {
                            loading.style.display = 'none !important';
                            loading.style.visibility = 'hidden';
                        }
                        
                        // Forçar exibição do SVG
                        if (svg) {
                            svg.style.display = 'block !important';
                            svg.style.visibility = 'visible';
                            svg.style.opacity = '1';
                        }
                        
                        console.log('🔧 Teste adicional concluído');
                    }, 1000);
                }, 2000);
                
            } catch (error) {
                console.error('❌ Erro:', error);
                document.getElementById('loading-test').innerHTML = `
                    <div class="text-center">
                        <h5 class="text-danger">Erro: ${error.message}</h5>
                    </div>
                `;
            }
            
            // Botão de teste manual
            document.getElementById('manual-test').addEventListener('click', function() {
                console.log('🔧 Teste manual iniciado');
                
                const loading = document.getElementById('loading-test');
                const svg = document.getElementById('test-svg');
                
                if (loading) {
                    loading.style.display = 'none';
                    console.log('✅ Loading forçado a ocultar');
                }
                
                if (svg) {
                    svg.style.display = 'block';
                    svg.style.visibility = 'visible';
                    svg.style.opacity = '1';
                    console.log('✅ SVG forçado a mostrar');
                }
                
                document.getElementById('test-status').textContent = 'Teste manual executado! Verificando SVG...';
                
                setTimeout(() => {
                    const svgRect = svg ? svg.getBoundingClientRect() : null;
                    console.log('SVG rect:', svgRect);
                    document.getElementById('test-status').textContent = `SVG: ${svgRect ? 'Visível' : 'Não encontrado'} - ${svgRect ? svgRect.width + 'x' + svgRect.height : 'N/A'}`;
                }, 500);
            });
        });
    </script>
@endsection