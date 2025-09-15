<?php

    $image = get_field('image_profile');
    $name = get_field('name_lastname');
    $role = get_field('role_profile'); 
    $bio = get_field('bio');
    $social_1 = get_field('social_1');
    $social_2 = get_field('social_2');
    $social_3 = get_field('social_3');
    $website = get_field('website');

    // ancora html
    $anchor = '';
    if ( ! empty( $block['anchor'] ) ) {
        $anchor = esc_attr( $block['anchor'] );
    }

    $class_name = 'personal-card';
    if ( ! empty( $block['className'] ) ) {
        $class_name .= ' ' . $block['className'];
    }
    if ( ! empty( $block['align'] ) ) {
        $class_name .= ' align' . $block['align'];
    }

?>

<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>">
    <?php if ( $image ) : ?>
        <figure class="personal-card__image">
            <?php echo wp_get_attachment_image( 
                    $image['ID'],
                    'full',
                    '',
                    array( 
                        'alt' => $image['alt'],
                        'class' => 'personal-card__img'
                    ) 
                ); 
            ?>
        </figure>
    <?php endif; ?>

    <?php if ( $name ) : ?>
        <h3 class="personal-card__name"><?php echo esc_html( $name ); ?></h3>
    <?php endif; ?>

    <?php if ( $role ) : ?>
        <div class="personal-card__role">
            <span><?php echo esc_html( $role ); ?></span>
        </div>
    <?php endif; ?>

    <?php if ( $bio ) : ?>
        <div class="personal-card__bio">
            <p><?php echo esc_html( $bio ); ?></p>
        </div>
    <?php endif; ?>

    <ul class="personal-card__socials">
        <li>
            <a href="<?php echo esc_url( $social_1 ); ?>" class="icon-instagram"></a>
        </li>
        <li>
            <a href="<?php echo esc_url( $social_2 ); ?>" class="icon-facebook"></a>
        </li>
        <li>
            <a href="<?php echo esc_url( $social_3 ); ?>" class="icon-linkedin-squared"></a>
        </li>
        <li>
            <a href="<?php echo esc_url( $website ); ?>" class="icon-globe"></a>
        </li>
    </ul>
</div>