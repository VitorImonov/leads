# leads

Enviar formulário para https://api.imonovsistemas.com.br/send method precisa ser POST. Os campos precisam ter os seguintes valores para a propriedade name
<img width="697" height="426" alt="image" src="https://github.com/user-attachments/assets/0b5bf6ab-c822-4ff6-a695-4cba1174455b" />
nome: name
celular: phone
email: email
texto de observação: observation
usuário: user

Além disso, o formulário deve ter dois campos hidden, um para a referência do imóvel, e outro para o usuário. 

Segue Exemplo:

<form id="formdetalhes" name="formdetalhes" class="form-2" action="https://api.imonovsistemas.com.br/send" method="POST">
              <input type="hidden" name="property_id" id="property_id" value="<?php echo $rstemp_imovel['ImoRef']; ?>">
              <a href="<?php echo $link_num_celular.'&text=Olá, estou interessado(a) no imóvel referência: '.$rstemp_imovel['ImoRef']; ?>"
                target="_blank"
                class="button-3 whats w-button">
                  WhatsApp
              </a>

              <div data-delay="0" class="dropdown w-dropdown">
                  <div class="dropdown-toggle dop2 w-dropdown-toggle">
                      <div style="font-family: Proxima nova; font-weight: 700;">
                          Envie-nos uma mensagem!
                      </div>
                  </div>

                  <nav class="dropdown-list list2 w-dropdown-list w--open">
                      <div class="div-block-84">
                          <div class="field-wrap first">
                              <label for="nome" class="form-field-label detalhes">Nome Completo</label>
                              <input type="text" class="form-field detalhes w-input" maxlength="256" name="name" required>
                          </div>

                          <div class="field-wrap detalhes2 second">
                              <label for="telefone" class="form-field-label detalhes">Telefone</label>
                              <input type="text" class="form-field detalhes w-input" maxlength="11" name="phone"
                                    placeholder="(xx) x xxxx-xxxx" required>
                          </div>
                      </div>

                      <div class="div-block-61 _2">
                          <div class="field-wrap oter">
                              <label for="email" class="form-field-label detalhes">E-mail</label>
                              <input type="email" class="form-field w-input" maxlength="256" name="email"
                                    placeholder="seuemail@email.com" required>
                          </div>

                          <div class="field-wrap-2 detalhes">
                              <label for="obs" class="form-field-label detalhes">Mensagem</label>
                              <textarea name="observation" maxlength="150" id="observation" class="textarea-2 _2 w-input"><?php echo ('Olá, estou interessado neste imóvel para'); ?><?php if ($rstemp_imovel['ImoStatus'] == 6) { echo 'Temporada'; } else { echo utf8_encode($rstemp_imovel['Cat1Nome']); } ?>de Ref.: <?php echo $rstemp_imovel['ImoRef']; ?>, <?php echo ('e gostaria de mais informações'); ?>.</textarea>
                          </div>
                      </div>
                      <input type="hidden" value="fiveh" name="user">
                      <input type="submit" value="Enviar" onclick="setFeedbackImmobile()" class="button-3 enviar _2 w-button">
                  </nav>
              </div>
          </form>

