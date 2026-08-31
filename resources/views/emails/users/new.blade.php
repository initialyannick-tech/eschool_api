<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    ```
    <title>Nouveau compte eSchool</title>
    ```

</head>

<body style="
    margin:0;
    padding:0;
    background:#f4f7fb;
    font-family:Arial, Helvetica, sans-serif;
    color:#334155;
">
<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background:#f4f7fb; padding:40px 15px;">

    <tr>
        <td align="center">

            <!-- CONTENEUR -->
            <table width="600" cellpadding="0" cellspacing="0" border="0"
                   style="
                       max-width:600px;
                       width:100%;
                       background:#ffffff;
                       border-radius:14px;
                       overflow:hidden;
                       box-shadow:0 5px 20px rgba(15,23,42,0.08);
                   ">

                <!-- HEADER -->
                <tr>
                    <td style="
                        background:#2563eb;
                        padding:28px 30px;
                        text-align:center;
                    ">

                        <!-- Logo par défaut eSchool -->
                        <div style="
                            width:64px;
                            height:64px;
                            margin:0 auto 12px auto;
                            background:#ffffff;
                            border-radius:14px;
                            line-height:64px;
                            font-size:28px;
                            font-weight:bold;
                            color:#2563eb;
                        ">
                            eS
                        </div>

                        <div style="
                            color:#ffffff;
                            font-size:25px;
                            font-weight:bold;
                            letter-spacing:0.5px;
                        ">
                            eSchool
                        </div>

                        <div style="
                            color:#dbeafe;
                            font-size:13px;
                            margin-top:5px;
                        ">
                            Plateforme de gestion scolaire
                        </div>

                    </td>
                </tr>


                <!-- CONTENU -->
                <tr>
                    <td style="padding:35px 35px 25px 35px;">

                        <h1 style="
                            margin:0 0 18px 0;
                            font-size:23px;
                            color:#1e293b;
                        ">
                            Bonjour {{ $user->prenom }} {{ $user->nom }},
                        </h1>

                        <p style="
                            margin:0 0 15px 0;
                            font-size:15px;
                            line-height:1.7;
                            color:#475569;
                        ">
                            Votre compte utilisateur a été créé avec succès
                            sur <strong>eSchool</strong>, votre plateforme de
                            gestion scolaire.
                        </p>

                        <p style="
                            margin:0 0 25px 0;
                            font-size:15px;
                            line-height:1.7;
                            color:#475569;
                        ">
                            Vous trouverez ci-dessous vos identifiants de
                            connexion.
                        </p>


                        <!-- IDENTIFIANTS -->
                        <table width="100%" cellpadding="0" cellspacing="0"
                               border="0"
                               style="
                                   background:#f8fafc;
                                   border:1px solid #e2e8f0;
                                   border-radius:10px;
                                   margin-bottom:25px;
                               ">

                            <tr>
                                <td style="padding:20px;">

                                    <p style="
                                        margin:0 0 12px 0;
                                        font-size:14px;
                                        color:#64748b;
                                    ">
                                        <strong style="color:#334155;">
                                            Adresse e-mail
                                        </strong>
                                    </p>

                                    <p style="
                                        margin:0 0 18px 0;
                                        font-size:15px;
                                        color:#1e293b;
                                    ">
                                        {{ $user->email }}
                                    </p>


                                    <p style="
                                        margin:0 0 12px 0;
                                        font-size:14px;
                                        color:#64748b;
                                    ">
                                        <strong style="color:#334155;">
                                            Mot de passe temporaire
                                        </strong>
                                    </p>

                                    <div style="
                                        display:inline-block;
                                        background:#ffffff;
                                        border:1px solid #cbd5e1;
                                        border-radius:7px;
                                        padding:10px 15px;
                                        font-size:16px;
                                        font-weight:bold;
                                        color:#2563eb;
                                        letter-spacing:1px;
                                    ">
                                        {{ $password }}
                                    </div>

                                </td>
                            </tr>

                        </table>


                        <!-- AVERTISSEMENT -->
                        <table width="100%" cellpadding="0" cellspacing="0"
                               border="0"
                               style="
                                   background:#eff6ff;
                                   border-left:4px solid #2563eb;
                                   margin-bottom:25px;
                               ">

                            <tr>
                                <td style="
                                    padding:15px 18px;
                                    font-size:14px;
                                    line-height:1.6;
                                    color:#475569;
                                ">

                                    <strong style="color:#1d4ed8;">
                                        Important
                                    </strong>

                                    <br>

                                    Pour protéger votre compte, nous vous
                                    recommandons de modifier votre mot de
                                    passe dès votre première connexion.

                                </td>
                            </tr>

                        </table>


                        <!-- BOUTON -->
                        <div style="text-align:center; margin:30px 0;">

                            <a href="{{ url('/login') }}"
                               style="
                                   display:inline-block;
                                   background:#2563eb;
                                   color:#ffffff;
                                   text-decoration:none;
                                   font-size:15px;
                                   font-weight:bold;
                                   padding:13px 30px;
                                   border-radius:8px;
                               ">
                                Accéder à eSchool
                            </a>

                        </div>


                        <p style="
                            margin:25px 0 0 0;
                            font-size:14px;
                            line-height:1.7;
                            color:#64748b;
                        ">
                            Si vous rencontrez un problème lors de votre
                            connexion, veuillez contacter l'administrateur
                            de votre établissement.
                        </p>

                        <p style="
                            margin:25px 0 0 0;
                            font-size:14px;
                            line-height:1.7;
                            color:#475569;
                        ">
                            Cordialement,<br>
                            <strong>L'équipe eSchool</strong>
                        </p>

                    </td>
                </tr>


                <!-- FOOTER -->
                <tr>
                    <td style="
                        background:#f8fafc;
                        border-top:1px solid #e2e8f0;
                        padding:20px 30px;
                        text-align:center;
                    ">

                        <p style="
                            margin:0 0 8px 0;
                            font-size:12px;
                            color:#64748b;
                        ">
                            © {{ date('Y') }} eSchool
                        </p>

                        <p style="
                            margin:0;
                            font-size:11px;
                            line-height:1.5;
                            color:#94a3b8;
                        ">
                            Vous recevez cet e-mail car un compte a été
                            créé pour vous sur la plateforme eSchool.
                            Si vous n'êtes pas à l'origine de cette
                            création, veuillez contacter le support.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>

</table>
</body>
</html>
