@extends('components.layouts.app')

@section('title', 'Assinatura Digital - Proposição #' . $proposicao->id)

@section('content')
<div id="assinatura-digital-app">
    <assinatura-digital
        :proposicao="{{ json_encode($proposicao) }}"
        :certificado-cadastrado="{{ json_encode($certificadoCadastrado) }}"
        :certificado-valido="{{ json_encode($certificadoValido) }}"
        :dados-certificado="{{ json_encode($dadosCertificado) }}"
        :senha-salva="{{ json_encode($senhaSalva) }}"
        :dados-usuario="{{ json_encode([
            'id' => Auth::id(),
            'nome' => Auth::user()->name,
            'email' => Auth::user()->email,
            'ip' => request()->ip()
        ]) }}"
        :pdf-url="'{{ route('proposicoes.pdf-original', $proposicao) }}'"
    ></assinatura-digital>
</div>
@endsection

@push('scripts')
<!-- Vue.js 3 CDN -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
const { createApp } = Vue

// Componente AssinaturaDigital inline (será substituído pela importação do arquivo .vue)
const AssinaturaDigitalComponent = {
  name: 'AssinaturaDigital',
  props: {
    proposicao: Object,
    certificadoCadastrado: Boolean,
    certificadoValido: Boolean,
    dadosCertificado: Object,
    senhaSalva: Boolean,
    dadosUsuario: Object,
    pdfUrl: String
  },
  
  template: `<div class="assinatura-digital-wrapper">
      <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
          <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
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
            <div class="d-flex align-items-center gap-2 gap-lg-3">
              <button type="button" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-secondary btn-active-light-secondary" @click="voltar">
                <i class="ki-duotone ki-arrow-left fs-2">
                  <span class="path1"></span>
                  <span class="path2"></span>
                </i>Voltar
              </button>
            </div>
          </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
          <div id="kt_app_content_container" class="app-container container-xxl">

            <div class="row g-7">
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
                          <span class="badge badge-light-primary">@{{ proposicao.tipo }}</span>
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
                        <div class="fw-bold text-end">@{{ proposicao.numero_temporario || 'Aguardando' }}</div>
                      </div>
                      
                      <div class="d-flex align-items-center justify-content-between mb-5">
                        <div class="fw-semibold">
                          <i class="ki-duotone ki-calendar text-gray-400 fs-6 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                          </i>Data Criação:
                        </div>
                        <div class="fw-bold text-end">@{{ formatDate(proposicao.created_at) }}</div>
                      </div>
                      
                      <div class="d-flex align-items-center justify-content-between mb-5">
                        <div class="fw-semibold">
                          <i class="ki-duotone ki-flash text-gray-400 fs-6 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                          </i>Urgência:
                        </div>
                        <div class="fw-bold text-end">
                          <span v-if="proposicao.urgencia === 'urgentissima'" class="badge badge-light-danger">Urgentissima</span>
                          <span v-else-if="proposicao.urgencia === 'urgente'" class="badge badge-light-warning">Urgente</span>
                          <span v-else class="badge badge-light-secondary">Normal</span>
                        </div>
                      </div>
                    </div>
                    
                    <div class="separator separator-dashed my-5"></div>
                    
                    <div class="mb-5">
                      <label class="fs-6 fw-semibold mb-2 text-gray-600">Título:</label>
                      <div class="fw-bold text-gray-800">@{{ proposicao.titulo || 'Sem titulo' }}</div>
                    </div>
                    
                    <div class="mb-5">
                      <label class="fs-6 fw-semibold mb-2 text-gray-600">Ementa:</label>
                      <div class="fw-semibold text-gray-700 lh-lg">@{{ proposicao.ementa }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-8">
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
                        <h4 class="mb-3">@{{ certificadoStatusTitle }}</h4>
                        
                        <div class="row g-3 mb-4">
                          <div class="col-md-6">
                            <div class="d-flex align-items-center">
                              <i class="fas fa-id-card text-primary me-2"></i>
                              <div>
                                <div class="fs-7 text-muted">CN (Common Name)</div>
                                <div class="fw-bold">@{{ certificado.dados.cn }}</div>
                              </div>
                            </div>
                          </div>
                          
                          <div class="col-md-6">
                            <div class="d-flex align-items-center">
                              <i class="fas fa-calendar-alt text-info me-2"></i>
                              <div>
                                <div class="fs-7 text-muted">Válido até</div>
                                <div class="fw-bold d-flex align-items-center">
                                  @{{ formatDate(certificado.dados.validade) }}
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
                                  <span class="badge" :class="certificadoStatusBadge">@{{ certificadoStatusText }}</span>
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
                                    @{{ senhaStatusText }}
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

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
                    <div v-if="usarCertificadoCadastrado && certificado.detectado && certificado.valido">
                      <div class="alert alert-success d-flex align-items-center mb-6">
                        <i class="fas fa-check-circle fs-2 me-4 text-success"></i>
                        <div>
                          <h4 class="mb-2">Pronto para Assinatura!</h4>
                          <p class="mb-1">Utilizando certificado digital cadastrado: <strong>@{{ certificado.dados.cn }}</strong></p>
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
                          @{{ erros.senha }}
                        </div>
                        <div class="form-text">
                          <i class="fas fa-info-circle me-1"></i>
                          Digite a senha do certificado digital cadastrado em seu perfil
                        </div>
                      </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-center mb-6">
                      <i class="fas fa-info-circle fs-2 me-4 text-info"></i>
                      <div>
                        <h6 class="mb-2">Informações da Assinatura</h6>
                        <p class="mb-1"><strong>Assinante:</strong> @{{ dadosUsuario.nome }}</p>
                        <p class="mb-1"><strong>Data:</strong> @{{ dataAtual }}</p>
                        <p class="mb-0"><strong>IP:</strong> @{{ dadosUsuario.ip }}</p>
                      </div>
                    </div>

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
                        @{{ botaoAssinaturaTexto }}
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
  `,
  
  data() {
    return {
      // Estados do componente
      processando: false,
      pdfLoading: true,
      senhaVisivel: false,
      
      // Formulário
      usarCertificadoCadastrado: true,
      senhaFormulario: '',
      
      // Validação
      erros: {
        senha: null
      }
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
        ? 'Salva (Automatica)' 
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
      }
      return false
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
      }
    },
    
    mostrarBemVindo() {
      const titulo = this.certificado.dados.senha_salva 
        ? 'Certificado Pronto!'
        : 'Certificado Detectado!'
        
      const mensagem = this.certificado.dados.senha_salva
        ? 'Seu certificado está configurado para assinatura automática.'
        : 'Certificado válido encontrado. A senha será solicitada.'
        
      Swal.fire({
        title: titulo,
        html: '<div class="text-center">' +
          '<i class="fas fa-certificate fs-2x text-success mb-3"></i>' +
          '<p class="mb-0">' + mensagem + '</p>' +
        '</div>',
        icon: null,
        confirmButtonText: 'Continuar',
        confirmButtonColor: '#1BC5BD',
        timer: 4000,
        timerProgressBar: true
      })
    },
    
    mostrarCertificadoExpirado() {
      Swal.fire({
        title: 'Certificado Expirado',
        html: '<div class="text-center">' +
          '<i class="fas fa-exclamation-triangle fs-2x text-warning mb-3"></i>' +
          '<p>O certificado cadastrado está expirado.</p>' +
          '<p class="mb-0">Cadastre um novo certificado para usar esta funcionalidade.</p>' +
        '</div>',
        icon: null,
        confirmButtonText: 'Cadastrar Novo',
        confirmButtonColor: '#FFA800'
      }).then(() => {
        window.location.href = '/parlamentares/' + this.dadosUsuario.id + '/edit'
      })
    },
    
    mostrarOpcaoManual() {
      this.usarCertificadoCadastrado = false
    },
    
    validarSenha() {
      this.erros.senha = null
      if (this.senhaFormulario.length < 4) {
        this.erros.senha = 'Senha deve ter pelo menos 4 caracteres'
      }
    },
    
    toggleSenhaVisibilidade() {
      this.senhaVisivel = !this.senhaVisivel
      const input = document.getElementById('senha_certificado')
      if (input) {
        input.type = this.senhaVisivel ? 'text' : 'password'
      }
    },
    
    async processarAssinatura() {
      if (!this.formularioValido || this.processando) return
      
      this.processando = true
      
      try {
        // Preparar dados
        const formData = new FormData()
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content)
        formData.append('usar_certificado_cadastrado', '1')
        
        if (!this.certificado.dados.senha_salva) {
          formData.append('senha_certificado', this.senhaFormulario)
        }
        
        // Enviar requisição
        const response = await fetch('/proposicoes/' + this.proposicao.id + '/assinatura-digital/processar', {
          method: 'POST',
          body: formData
        })
        
        const result = await response.json()
        
        if (result.success) {
          await Swal.fire({
            title: 'Sucesso!',
            html: '<div class="text-center">' +
              '<i class="fas fa-check-circle fs-2x text-success mb-3"></i>' +
              '<p>' + result.message + '</p>' +
            '</div>',
            icon: null,
            confirmButtonText: 'Ver Documento',
            confirmButtonColor: '#1BC5BD'
          })
          
          // Redirecionar
          window.location.href = result.redirect || '/proposicoes/' + this.proposicao.id
        } else {
          throw new Error(result.message)
        }
        
      } catch (error) {
        Swal.fire({
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
      link.download = 'proposicao-' + this.proposicao.id + '.pdf'
      link.click()
    },
    
    voltar() {
      window.history.back()
    },
    
    formatDate(date) {
      if (!date) return 'N/A'
      return new Date(date).toLocaleDateString('pt-BR')
    }
  }
}

// Criar aplicação Vue
const app = createApp({
  components: {
    'assinatura-digital': AssinaturaDigitalComponent
  }
})

// Montar aplicação
app.mount('#assinatura-digital-app')
</script>
@endpush

@push('styles')
<style>
.assinatura-digital-wrapper {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.card {
  box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
  border: 0;
  margin-bottom: 2rem;
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

.pdf-viewer-container iframe {
  border-radius: 6px;
}

/* Animações */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.6s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
  .row.g-7 {
    --bs-gutter-x: 1rem;
  }
  
  .card-body {
    padding: 1.5rem;
  }
  
  .d-flex.gap-3 {
    flex-direction: column;
  }
  
  .d-flex.gap-3 .btn {
    width: 100%;
  }
}

/* Melhor espaçamento seguindo o template */
.app-container {
  max-width: 1400px;
}

.card-header {
  padding: 1.75rem 2rem 0 2rem;
}

.card-body {
  padding: 2rem;
}

.row.g-7 {
  --bs-gutter-x: 2rem;
  --bs-gutter-y: 2rem;
}

.mb-6 {
  margin-bottom: 2rem !important;
}

.fs-7 {
  font-size: 0.85rem !important;
}

.fw-semibold {
  font-weight: 600 !important;
}

.text-gray-600 {
  color: #7e8299 !important;
}

.text-gray-700 {
  color: #5e6278 !important;
}

.text-gray-800 {
  color: #3f4254 !important;
}

/* Badges personalizados */
.badge-light-primary {
  color: #3699ff;
  background-color: rgba(54, 153, 255, 0.1);
}

.badge-light-success {
  color: #1bc5bd;
  background-color: rgba(27, 197, 189, 0.1);
}

.badge-light-warning {
  color: #ffa800;
  background-color: rgba(255, 168, 0, 0.1);
}

.badge-light-danger {
  color: #f64e60;
  background-color: rgba(246, 78, 96, 0.1);
}

.badge-light-secondary {
  color: #7e8299;
  background-color: rgba(126, 130, 153, 0.1);
}
</style>
@endpush