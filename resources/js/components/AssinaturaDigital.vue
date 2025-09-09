<template>
  <div class="assinatura-digital-wrapper">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
      <!--begin::Toolbar-->
      <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
          <!--begin::Page title-->
          <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
              Assinatura Digital
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
              <li class="breadcrumb-item text-muted">
                <a href="#" class="text-muted text-hover-primary">Dashboard</a>
              </li>
              <li class="breadcrumb-item">
                <span class="bullet bg-gray-400 w-5px h-2px"></span>
              </li>
              <li class="breadcrumb-item text-muted">Assinatura Digital</li>
            </ul>
          </div>
        </div>
      </div>

      <!--begin::Content-->
      <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

          <div class="row g-7">
            <!--begin::Sidebar - Informações da Proposição-->
            <div class="col-xl-4">
              <div class="card card-flush mb-6 mb-xl-9">
                <div class="card-header mt-5">
                  <div class="card-title flex-column">
                    <h2 class="mb-1">Informações da Proposição</h2>
                    <div class="fs-6 fw-semibold text-muted">Revise os dados antes de assinar</div>
                  </div>
                </div>
                
                <div class="card-body pt-0">
                  <div class="d-flex flex-column text-gray-600">
                    <div class="d-flex align-items-center justify-content-between mb-5">
                      <div class="fw-semibold">
                        <i class="ki-duotone ki-profile-circle text-gray-400 fs-6 me-2">
                          <span class="path1"></span>
                          <span class="path2"></span>
                          <span class="path3"></span>
                        </i>Tipo:
                      </div>
                      <div class="fw-bold text-end">
                        <span class="badge badge-light-primary">{{ proposicao.tipo }}</span>
                      </div>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-5">
                      <div class="fw-semibold">
                        <i class="ki-duotone ki-tag text-gray-400 fs-6 me-2">
                          <span class="path1"></span>
                          <span class="path2"></span>
                          <span class="path3"></span>
                        </i>Número:
                      </div>
                      <div class="fw-bold text-end">{{ proposicao.numero_temporario || 'Aguardando' }}</div>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-5">
                      <div class="fw-semibold">
                        <i class="ki-duotone ki-calendar text-gray-400 fs-6 me-2">
                          <span class="path1"></span>
                          <span class="path2"></span>
                        </i>Data Criação:
                      </div>
                      <div class="fw-bold text-end">{{ formatDate(proposicao.created_at) }}</div>
                    </div>
                  </div>
                  
                  <div class="separator separator-dashed my-5"></div>
                  
                  <div class="mb-5">
                    <label class="fs-6 fw-semibold mb-2 text-gray-600">Título:</label>
                    <div class="fw-bold text-gray-800">{{ proposicao.titulo || 'Sem título' }}</div>
                  </div>
                  
                  <div class="mb-5">
                    <label class="fs-6 fw-semibold mb-2 text-gray-600">Ementa:</label>
                    <div class="fw-semibold text-gray-700 lh-lg">{{ proposicao.ementa }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!--begin::Main Content - Assinatura-->
            <div class="col-xl-8">
              <!-- Certificado Detectado -->
              <div v-if="certificado.detectado" class="card card-flush mb-6">
                <div class="card-header">
                  <div class="card-title">
                    <h2 class="fw-bold text-dark">
                      <i class="fas fa-certificate me-2" :class="certificadoIconClass"></i>
                      Certificado Digital Detectado
                    </h2>
                  </div>
                </div>
                
                <div class="card-body">
                  <div :class="certificadoAlertClass" class="d-flex align-items-start p-5 mb-6">
                    <i :class="certificadoStatusIcon" class="fs-2x me-4"></i>
                    <div class="flex-grow-1">
                      <h4 class="mb-3">{{ certificadoStatusTitle }}</h4>
                      
                      <div class="row g-3 mb-4">
                        <div class="col-md-6">
                          <div class="d-flex align-items-center">
                            <i class="fas fa-id-card text-primary me-2"></i>
                            <div>
                              <div class="fs-7 text-muted">CN (Common Name)</div>
                              <div class="fw-bold">{{ certificado.dados.cn }}</div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-6">
                          <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-info me-2"></i>
                            <div>
                              <div class="fs-7 text-muted">Válido até</div>
                              <div class="fw-bold d-flex align-items-center">
                                {{ formatDate(certificado.dados.validade) }}
                                <span v-if="!certificado.valido" class="badge badge-danger ms-2 fs-8">EXPIRADO</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-6">
                          <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            <div>
                              <div class="fs-7 text-muted">Status</div>
                              <div class="fw-bold">
                                <span class="badge" :class="certificadoStatusBadge">{{ certificadoStatusText }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-6">
                          <div class="d-flex align-items-center">
                            <i class="fas fa-key text-warning me-2"></i>
                            <div>
                              <div class="fs-7 text-muted">Senha</div>
                              <div class="fw-bold">
                                <span class="badge" :class="senhaBadgeClass">
                                  <i :class="senhaIconClass" class="me-1"></i>
                                  {{ senhaStatusText }}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Ações do Certificado -->
                      <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                          <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="usar_certificado_cadastrado"
                            v-model="usarCertificadoCadastrado"
                            :disabled="!certificado.valido"
                          >
                          <label class="form-check-label fw-semibold" for="usar_certificado_cadastrado">
                            Usar Certificado Cadastrado
                          </label>
                        </div>
                        
                        <button 
                          v-if="!certificado.valido" 
                          type="button" 
                          class="btn btn-sm btn-outline-warning"
                          @click="mostrarOpcaoManual"
                        >
                          <i class="fas fa-upload me-1"></i>
                          Usar Opção Manual
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- PDF Viewer -->
              <div class="card card-flush mb-6">
                <div class="card-header align-items-start">
                  <div class="card-title">
                    <h2 class="fw-bold text-dark">
                      <i class="fas fa-file-pdf text-danger me-2"></i>
                      Documento para Assinatura
                    </h2>
                  </div>
                  
                  <div class="card-toolbar">
                    <div class="d-flex gap-2">
                      <button 
                        type="button" 
                        class="btn btn-sm btn-outline btn-outline-primary"
                        @click="abrirPDF"
                      >
                        <i class="fas fa-external-link-alt me-1"></i>Abrir PDF
                      </button>
                      <button 
                        type="button" 
                        class="btn btn-sm btn-outline btn-outline-secondary"
                        @click="downloadPDF"
                      >
                        <i class="fas fa-download me-1"></i>Download
                      </button>
                    </div>
                  </div>
                </div>
                
                <div class="card-body pt-0">
                  <div class="pdf-viewer-container position-relative" style="height: 500px; border: 1px solid #e1e3ea; border-radius: 8px; overflow: hidden; background-color: #f8f9fa;">
                    <div v-if="pdfLoading" class="position-absolute top-50 start-50 translate-middle text-center">
                      <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Carregando...</span>
                      </div>
                      <p class="text-muted">Carregando documento PDF...</p>
                    </div>
                    
                    <iframe 
                      v-show="!pdfLoading"
                      :src="pdfUrl" 
                      width="100%" 
                      height="100%" 
                      frameborder="0"
                      @load="pdfLoading = false"
                    ></iframe>
                  </div>
                </div>
              </div>

              <!-- Formulário de Assinatura -->
              <div class="card card-flush">
                <div class="card-header">
                  <div class="card-title">
                    <h2 class="fw-bold text-dark">
                      <i class="fas fa-signature me-2"></i>
                      Processo de Assinatura
                    </h2>
                  </div>
                </div>
                
                <div class="card-body">
                  <!-- Certificado Cadastrado -->
                  <div v-if="usarCertificadoCadastrado && certificado.detectado && certificado.valido">
                    <div class="alert alert-success d-flex align-items-center mb-6">
                      <i class="fas fa-check-circle fs-2 me-4 text-success"></i>
                      <div>
                        <h4 class="mb-2">Pronto para Assinatura!</h4>
                        <p class="mb-1">Utilizando certificado digital cadastrado: <strong>{{ certificado.dados.cn }}</strong></p>
                        <p class="mb-0">
                          <span v-if="certificado.dados.senha_salva" class="text-success">
                            <i class="fas fa-magic me-1"></i>Assinatura automática habilitada
                          </span>
                          <span v-else class="text-warning">
                            <i class="fas fa-key me-1"></i>Senha será solicitada
                          </span>
                        </p>
                      </div>
                    </div>

                    <!-- Campo de Senha (se necessário) -->
                    <div v-if="!certificado.dados.senha_salva" class="mb-6">
                      <label for="senha_certificado" class="form-label required fw-bold">
                        <i class="fas fa-key me-2"></i>Senha do Certificado
                      </label>
                      <div class="input-group">
                        <input 
                          type="password" 
                          class="form-control form-control-lg" 
                          id="senha_certificado"
                          v-model="senhaFormulario"
                          placeholder="Digite a senha do certificado..."
                          :class="{ 'is-invalid': erros.senha }"
                          @input="validarSenha"
                        >
                        <button 
                          class="btn btn-outline-secondary" 
                          type="button"
                          @click="toggleSenhaVisibilidade"
                        >
                          <i :class="senhaVisivel ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                      </div>
                      <div v-if="erros.senha" class="invalid-feedback d-block">
                        {{ erros.senha }}
                      </div>
                      <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Digite a senha do certificado digital cadastrado em seu perfil
                      </div>
                    </div>
                  </div>

                  <!-- Opções Tradicionais -->
                  <div v-else>
                    <!-- Tipos de Certificado -->
                    <div class="mb-6">
                      <label class="form-label required fw-bold mb-4">Tipo de Certificado Digital</label>
                      <div class="row g-4">
                        <div class="col-lg-4" v-for="tipo in tiposCertificado" :key="tipo.value">
                          <div 
                            class="certificado-option card h-100 cursor-pointer"
                            :class="{ 'border-primary bg-light-primary': tipoCertificadoSelecionado === tipo.value }"
                            @click="selecionarTipoCertificado(tipo.value)"
                          >
                            <div class="card-body text-center p-6">
                              <div class="mb-4">
                                <i :class="tipo.icon" class="fs-3x" :style="{ color: tipo.color }"></i>
                              </div>
                              <h5 class="fw-bold mb-2">{{ tipo.nome }}</h5>
                              <p class="text-muted fs-7 mb-0">{{ tipo.descricao }}</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Upload PFX -->
                    <div v-if="tipoCertificadoSelecionado === 'PFX'" class="mb-6">
                      <label class="form-label required fw-bold">Arquivo do Certificado</label>
                      <div 
                        class="file-upload-area border-dashed border-2 rounded p-6 text-center cursor-pointer"
                        :class="{ 'border-primary bg-light-primary': dragOver }"
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop.prevent="handleFileDrop"
                        @click="$refs.fileInput.click()"
                      >
                        <input 
                          type="file" 
                          ref="fileInput" 
                          accept=".pfx,.p12" 
                          class="d-none"
                          @change="handleFileSelect"
                        >
                        
                        <div v-if="!arquivoSelecionado">
                          <i class="fas fa-cloud-upload-alt fs-3x text-primary mb-3"></i>
                          <h5 class="fw-bold mb-2">Clique ou arraste o arquivo aqui</h5>
                          <p class="text-muted mb-0">Formatos aceitos: .pfx, .p12</p>
                        </div>
                        
                        <div v-else class="d-flex align-items-center justify-content-center">
                          <i class="fas fa-file text-success fs-2 me-3"></i>
                          <div>
                            <div class="fw-bold">{{ arquivoSelecionado.name }}</div>
                            <div class="text-muted fs-7">{{ formatFileSize(arquivoSelecionado.size) }}</div>
                          </div>
                          <button 
                            type="button" 
                            class="btn btn-sm btn-light-danger ms-3"
                            @click.stop="removerArquivo"
                          >
                            <i class="fas fa-times"></i>
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Senha para todos os tipos -->
                    <div v-if="tipoCertificadoSelecionado" class="mb-6">
                      <label for="senha_tradicional" class="form-label required fw-bold">
                        Senha do Certificado
                      </label>
                      <div class="input-group">
                        <input 
                          type="password" 
                          class="form-control form-control-lg" 
                          id="senha_tradicional"
                          v-model="senhaFormulario"
                          placeholder="Digite a senha..."
                          :class="{ 'is-invalid': erros.senha }"
                        >
                        <button 
                          class="btn btn-outline-secondary" 
                          type="button"
                          @click="toggleSenhaVisibilidade"
                        >
                          <i :class="senhaVisivel ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                      </div>
                      <div v-if="erros.senha" class="invalid-feedback d-block">
                        {{ erros.senha }}
                      </div>
                    </div>
                  </div>

                  <!-- Informações da Assinatura -->
                  <div class="alert alert-info d-flex align-items-center mb-6">
                    <i class="fas fa-info-circle fs-2 me-4 text-info"></i>
                    <div>
                      <h6 class="mb-2">Informações da Assinatura</h6>
                      <p class="mb-1"><strong>Assinante:</strong> {{ dadosUsuario.nome }}</p>
                      <p class="mb-1"><strong>Data:</strong> {{ dataAtual }}</p>
                      <p class="mb-0"><strong>IP:</strong> {{ dadosUsuario.ip }}</p>
                    </div>
                  </div>

                  <!-- Botões de Ação -->
                  <div class="d-flex justify-content-end gap-3">
                    <button 
                      type="button" 
                      class="btn btn-secondary"
                      @click="voltar"
                      :disabled="processando"
                    >
                      <i class="fas fa-arrow-left me-1"></i>
                      Voltar
                    </button>
                    
                    <button 
                      type="button" 
                      class="btn"
                      :class="botaoAssinaturaCss"
                      @click="processarAssinatura"
                      :disabled="!formularioValido || processando"
                    >
                      <span v-if="processando" class="spinner-border spinner-border-sm me-2"></span>
                      <i v-else :class="botaoAssinaturaIcon" class="me-1"></i>
                      {{ botaoAssinaturaTexto }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AssinaturaDigital',
  props: {
    proposicao: {
      type: Object,
      required: true
    },
    certificadoCadastrado: {
      type: Boolean,
      default: false
    },
    certificadoValido: {
      type: Boolean,
      default: false
    },
    dadosCertificado: {
      type: Object,
      default: null
    },
    senhaSalva: {
      type: Boolean,
      default: false
    },
    dadosUsuario: {
      type: Object,
      required: true
    },
    pdfUrl: {
      type: String,
      required: true
    }
  },
  
  data() {
    return {
      // Estados do componente
      processando: false,
      pdfLoading: true,
      dragOver: false,
      senhaVisivel: false,
      
      // Formulário
      usarCertificadoCadastrado: true,
      tipoCertificadoSelecionado: null,
      senhaFormulario: '',
      arquivoSelecionado: null,
      
      // Validação
      erros: {
        senha: null,
        arquivo: null
      },
      
      // Tipos de certificado
      tiposCertificado: [
        {
          value: 'A1',
          nome: 'Certificado A1',
          descricao: 'Arquivo instalado no computador',
          icon: 'fas fa-laptop',
          color: '#3699FF'
        },
        {
          value: 'A3', 
          nome: 'Certificado A3',
          descricao: 'Token/Smartcard físico',
          icon: 'fas fa-usb-drive',
          color: '#F64E60'
        },
        {
          value: 'PFX',
          nome: 'Upload .PFX',
          descricao: 'Enviar arquivo de certificado',
          icon: 'fas fa-cloud-upload-alt',
          color: '#1BC5BD'
        }
      ]
    }
  },
  
  computed: {
    certificado() {
      return {
        detectado: this.certificadoCadastrado,
        valido: this.certificadoValido,
        dados: this.dadosCertificado || {}
      }
    },
    
    certificadoIconClass() {
      return this.certificado.valido ? 'text-success' : 'text-warning'
    },
    
    certificadoAlertClass() {
      return this.certificado.valido 
        ? 'alert alert-success' 
        : 'alert alert-warning'
    },
    
    certificadoStatusIcon() {
      return this.certificado.valido 
        ? 'fas fa-check-circle text-success'
        : 'fas fa-exclamation-triangle text-warning'
    },
    
    certificadoStatusTitle() {
      return this.certificado.valido 
        ? 'Certificado Digital Válido'
        : 'Certificado Digital Expirado'
    },
    
    certificadoStatusBadge() {
      return this.certificado.valido 
        ? 'badge-success' 
        : 'badge-danger'
    },
    
    certificadoStatusText() {
      return this.certificado.valido ? 'Ativo' : 'Expirado'
    },
    
    senhaBadgeClass() {
      return this.certificado.dados.senha_salva 
        ? 'badge-success' 
        : 'badge-warning'
    },
    
    senhaIconClass() {
      return this.certificado.dados.senha_salva 
        ? 'fas fa-lock' 
        : 'fas fa-unlock'
    },
    
    senhaStatusText() {
      return this.certificado.dados.senha_salva 
        ? 'Salva (Automática)' 
        : 'Será solicitada'
    },
    
    botaoAssinaturaCss() {
      if (this.usarCertificadoCadastrado && this.certificado.dados.senha_salva) {
        return 'btn-success'
      }
      return 'btn-primary'
    },
    
    botaoAssinaturaIcon() {
      if (this.processando) return ''
      if (this.usarCertificadoCadastrado && this.certificado.dados.senha_salva) {
        return 'fas fa-magic'
      }
      return 'fas fa-signature'
    },
    
    botaoAssinaturaTexto() {
      if (this.processando) return 'Processando...'
      if (this.usarCertificadoCadastrado && this.certificado.dados.senha_salva) {
        return 'Assinar Automaticamente'
      }
      return 'Assinar Documento'
    },
    
    formularioValido() {
      if (this.usarCertificadoCadastrado && this.certificado.detectado) {
        if (!this.certificado.valido) return false
        if (!this.certificado.dados.senha_salva && !this.senhaFormulario) return false
        return true
      } else {
        if (!this.tipoCertificadoSelecionado) return false
        if (!this.senhaFormulario) return false
        if (this.tipoCertificadoSelecionado === 'PFX' && !this.arquivoSelecionado) return false
        return true
      }
    },
    
    dataAtual() {
      return new Date().toLocaleString('pt-BR')
    }
  },
  
  mounted() {
    this.inicializar()
  },
  
  methods: {
    inicializar() {
      // Configurar estado inicial
      if (this.certificado.detectado && this.certificado.valido) {
        this.usarCertificadoCadastrado = true
        this.mostrarBemVindo()
      } else if (this.certificado.detectado && !this.certificado.valido) {
        this.mostrarCertificadoExpirado()
      } else {
        this.mostrarSelecaoTipo()
      }
    },
    
    mostrarBemVindo() {
      const titulo = this.certificado.dados.senha_salva 
        ? 'Certificado Pronto!'
        : 'Certificado Detectado!'
        
      const mensagem = this.certificado.dados.senha_salva
        ? 'Seu certificado está configurado para assinatura automática.'
        : 'Certificado válido encontrado. A senha será solicitada.'
        
      this.$swal.fire({
        title: titulo,
        html: `<div class="text-center">
          <i class="fas fa-certificate fs-2x text-success mb-3"></i>
          <p class="mb-0">${mensagem}</p>
        </div>`,
        icon: null,
        confirmButtonText: 'Continuar',
        confirmButtonColor: '#1BC5BD',
        timer: 4000,
        timerProgressBar: true
      })
    },
    
    mostrarCertificadoExpirado() {
      this.$swal.fire({
        title: 'Certificado Expirado',
        html: `<div class="text-center">
          <i class="fas fa-exclamation-triangle fs-2x text-warning mb-3"></i>
          <p>O certificado cadastrado está expirado.</p>
          <p class="mb-0">Você pode usar a opção manual ou cadastrar um novo certificado.</p>
        </div>`,
        icon: null,
        confirmButtonText: 'Usar Opção Manual',
        confirmButtonColor: '#FFA800',
        showCancelButton: true,
        cancelButtonText: 'Cadastrar Novo'
      }).then((result) => {
        if (result.isConfirmed) {
          this.mostrarOpcaoManual()
        } else if (result.dismiss !== this.$swal.DismissReason.cancel) {
          // Redirecionar para cadastro
          window.location.href = `/parlamentares/${this.dadosUsuario.id}/edit`
        }
      })
    },
    
    mostrarSelecaoTipo() {
      this.$swal.fire({
        title: 'Assinatura Digital',
        text: 'Selecione o tipo de certificado para iniciar o processo.',
        icon: 'info',
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end'
      })
    },
    
    mostrarOpcaoManual() {
      this.usarCertificadoCadastrado = false
      this.$nextTick(() => {
        // Scroll para seção de tipos
        document.querySelector('.certificado-option')?.scrollIntoView({ 
          behavior: 'smooth',
          block: 'center'
        })
      })
    },
    
    selecionarTipoCertificado(tipo) {
      this.tipoCertificadoSelecionado = tipo
      this.erros.senha = null
      
      // Mostrar informações sobre o tipo
      const tipoInfo = this.tiposCertificado.find(t => t.value === tipo)
      if (tipoInfo) {
        this.$swal.fire({
          title: tipoInfo.nome,
          text: tipoInfo.descricao,
          icon: 'info',
          confirmButtonText: 'Continuar',
          timer: 2000
        })
      }
    },
    
    handleFileSelect(event) {
      const file = event.target.files[0]
      if (file) {
        this.validarArquivo(file)
      }
    },
    
    handleFileDrop(event) {
      this.dragOver = false
      const files = event.dataTransfer.files
      if (files.length > 0) {
        this.validarArquivo(files[0])
      }
    },
    
    validarArquivo(file) {
      this.erros.arquivo = null
      
      // Validar extensão
      const extensoesValidas = ['.pfx', '.p12']
      const extensao = '.' + file.name.split('.').pop().toLowerCase()
      
      if (!extensoesValidas.includes(extensao)) {
        this.erros.arquivo = 'Formato não suportado. Use .pfx ou .p12'
        return
      }
      
      // Validar tamanho (máximo 5MB)
      if (file.size > 5 * 1024 * 1024) {
        this.erros.arquivo = 'Arquivo muito grande. Máximo 5MB'
        return
      }
      
      this.arquivoSelecionado = file
    },
    
    removerArquivo() {
      this.arquivoSelecionado = null
      this.$refs.fileInput.value = ''
    },
    
    validarSenha() {
      this.erros.senha = null
      if (this.senhaFormulario.length < 4) {
        this.erros.senha = 'Senha deve ter pelo menos 4 caracteres'
      }
    },
    
    toggleSenhaVisibilidade() {
      this.senhaVisivel = !this.senhaVisivel
      const input = document.getElementById(this.usarCertificadoCadastrado ? 'senha_certificado' : 'senha_tradicional')
      input.type = this.senhaVisivel ? 'text' : 'password'
    },
    
    async processarAssinatura() {
      if (!this.formularioValido || this.processando) return
      
      this.processando = true
      
      try {
        // Preparar dados
        const formData = new FormData()
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content)
        
        if (this.usarCertificadoCadastrado) {
          formData.append('usar_certificado_cadastrado', '1')
          if (!this.certificado.dados.senha_salva) {
            formData.append('senha_certificado', this.senhaFormulario)
          }
        } else {
          formData.append('tipo_certificado', this.tipoCertificadoSelecionado)
          formData.append('senha_certificado', this.senhaFormulario)
          
          if (this.arquivoSelecionado) {
            formData.append('arquivo_certificado', this.arquivoSelecionado)
          }
        }
        
        // Enviar requisição
        const response = await fetch(`/proposicoes/${this.proposicao.id}/assinatura-digital/processar`, {
          method: 'POST',
          body: formData
        })
        
        const result = await response.json()
        
        if (result.success) {
          await this.$swal.fire({
            title: 'Sucesso!',
            html: `<div class="text-center">
              <i class="fas fa-check-circle fs-2x text-success mb-3"></i>
              <p>${result.message}</p>
            </div>`,
            icon: null,
            confirmButtonText: 'Ver Documento',
            confirmButtonColor: '#1BC5BD'
          })
          
          // Redirecionar
          window.location.href = result.redirect || `/proposicoes/${this.proposicao.id}`
        } else {
          throw new Error(result.message)
        }
        
      } catch (error) {
        this.$swal.fire({
          title: 'Erro',
          text: error.message || 'Erro ao processar assinatura',
          icon: 'error',
          confirmButtonText: 'OK'
        })
      } finally {
        this.processando = false
      }
    },
    
    abrirPDF() {
      window.open(this.pdfUrl, '_blank')
    },
    
    downloadPDF() {
      const link = document.createElement('a')
      link.href = this.pdfUrl
      link.download = `proposicao-${this.proposicao.id}.pdf`
      link.click()
    },
    
    voltar() {
      window.history.back()
    },
    
    formatDate(date) {
      if (!date) return 'N/A'
      return new Date(date).toLocaleDateString('pt-BR')
    },
    
    formatFileSize(bytes) {
      if (bytes === 0) return '0 B'
      const k = 1024
      const sizes = ['B', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }
  }
}
</script>

<style scoped>
.assinatura-digital-wrapper {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.certificado-option {
  transition: all 0.3s ease;
  cursor: pointer;
  border: 2px solid transparent;
}

.certificado-option:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.certificado-option.border-primary {
  border-color: var(--kt-primary) !important;
}

.file-upload-area {
  transition: all 0.3s ease;
  min-height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.file-upload-area:hover {
  border-color: var(--kt-primary) !important;
}

.pdf-viewer-container iframe {
  border-radius: 6px;
}

.card {
  box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
  border: 0;
}

.card-hover {
  transition: all 0.3s ease;
}

.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 1rem 3rem 1rem rgba(0, 0, 0, 0.175);
}

.btn {
  transition: all 0.3s ease;
}

.btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.alert {
  border: 0;
  border-radius: 0.75rem;
}

/* Animações */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideIn {
  from { transform: translateX(-20px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.card {
  animation: fadeIn 0.6s ease-out;
}

.certificado-option {
  animation: slideIn 0.4s ease-out;
}

.certificado-option:nth-child(2) { animation-delay: 0.1s; }
.certificado-option:nth-child(3) { animation-delay: 0.2s; }
.certificado-option:nth-child(4) { animation-delay: 0.3s; }

/* Responsive */
@media (max-width: 768px) {
  .row.g-7 {
    --kt-gutter-x: 1rem;
  }
  
  .card-body {
    padding: 1.5rem;
  }
}
</style>